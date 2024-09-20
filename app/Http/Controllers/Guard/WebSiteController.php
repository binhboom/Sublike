<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Library\CloudflareController;
use App\Models\PartnerWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\CpanelController;

class WebSiteController extends Controller
{
    public function viewCreateWebsite()
    {

        $website = PartnerWebsite::where('user_id', Auth::user()->id)->where('domain', request()->getHost())->first();

        if (!$website) {
            $website = new \stdClass();
            $website->id = null;
            $website->user_id = Auth::user()->id;
            $website->name = null;
            $website->domain = request()->getHost();
        }

        return view('guard.website.create', compact('website'));
    }

    public function createWebsite(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        if($user->level == 'member'){
            return redirect()->back()->with('error', 'Cấp bậc của bạn phải từ cộng tác viên trở lên!');
        }

        $website = PartnerWebsite::where('name', $request->domain)->where('user_id', '!=', Auth::user()->id)->first();

        if ($website) {
            return redirect()->back()->with('error', 'Tên miền đã tồn tại trong hệ thống!');
        } else {
            $website = PartnerWebsite::where('user_id', Auth::user()->id)->where('domain', request()->getHost())->first();

            if (!$website) {
                $website = new PartnerWebsite();
                $website->user_id = Auth::user()->id;
                $website->name = $request->domain;
                $website->url = 'https://' . $request->domain;
                $website->status = 'pending';
                $website->is_domain = site('is_domain') ?? env('APP_MAIN_SITE');
                $website->domain = request()->getHost();

                $cld = new CloudflareController();
                $add = $cld->addDomain($request->domain);
                if ($add['status'] == 'success') {
                    $zone_id = $add['data']['zone_id'];
                    $zone_status = $add['data']['zone_status'];
                    $zone_name = $add['data']['zone_name'];
                    $website->zone_data = json_encode($add['data']);
                    $website->zone_name = $zone_name;
                    $website->zone_id = $zone_id;
                    $website->zone_status = $zone_status;
                    $website->save();

                    $cpanel = new CpanelController();
                    $cpanel->createDomain($request->domain);
                    return redirect()->back()->with('success', 'Tạo website thành công!');
                } else {
                    return redirect()->back()->with('error', $add['message']);
                }
            }

            if ($website->name != $request->domain) {
                $cld = new CloudflareController();
                $add = $cld->addDomain($request->domain);
                if ($add['status'] === 'success') {
                    $cld->deleteDomain($website->zone_id);
                    $zone_id = $add['data']['zone_id'];
                    $zone_status = $add['data']['zone_status'];
                    $zone_name = $add['data']['zone_name'];
                    $website->name = $request->domain;
                    $website->url = 'https://' . $request->domain;
                    $website->zone_data = json_encode($add['data']);
                    $website->zone_name = $zone_name;
                    $website->zone_id = $zone_id;
                    $website->zone_status = $zone_status;
                    $website->name = $request->domain;
                    $website->status = 'pending';
                    $website->save();

                    $cpanel = new CpanelController();
                    $cpanel->deleteDomain($website->domain);
                    $cpanel->createDomain($request->domain);
                    return redirect()->back()->with('success', 'Tạo website thành công!');
                } else {
                    return redirect()->back()->with('error', $add['message']);
                }
            }
        }



        return redirect()->route('home');
    }
}
