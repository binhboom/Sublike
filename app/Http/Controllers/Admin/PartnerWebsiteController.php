<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\CloudflareController;
use App\Library\CpanelController;
use App\Models\ConfigSite;
use App\Models\PartnerWebsite;
use Illuminate\Http\Request;

class PartnerWebsiteController extends Controller
{
    public function viewPartnerWebsite()
    {
        $partnerWebsites = PartnerWebsite::where('is_domain', request()->getHost())->paginate(10);
        return view('admin.partner.website', compact('partnerWebsites'));
    }

    public function activePartnerWebsite($id)
    {
        $w = PartnerWebsite::where('id', $id)->where('is_domain', request()->getHost())->first();

        if (!$w) {
            return redirect()->back()->with('error', 'Đối tác chưa trỏ đến nameserver');
        }

        $json = json_decode($w->zone_data, true);
        $zone_id = $json['zone_id'];
        $dns_id = $json['dns_id'];
        $cld = new CloudflareController();

        $checkingStatus = $cld->infoDomain($w->zone_id);
        if (isset($checkingStatus) && $checkingStatus['success'] == true) {
            $w->zone_status = $checkingStatus['result']['status'];
            $w->save();
            if ($checkingStatus['result']['status'] == 'active') {
                $setTls = $cld->updateSslTls($zone_id, $dns_id);
                if ($setTls['success'] == true) {
                    $create = $cld->createDns($zone_id, $dns_id);
                    if ($create['success'] == true) {
                        $w->status = 'active';
                        $w->save();
                        return redirect()->back()->with('success', 'Kích hoạt Website đối tác thành công!');
                    } else {
                        if ($create['errors'][0]['code'] == 81057) {
                            $update = $cld->updateDns($zone_id, $dns_id);
                            if ($update['success'] == true) {

                                $w->status = 'active';
                                $w->save();

                                $configSite = new ConfigSite();
                                $configSite->name_site = $w->name;
                                $configSite->admin_username = $w->user->username;
                                $configSite->domain = $w->domain;
                                $configSite->save();

                                $cpanel = new CpanelController();
                                $cpanel->createDomain($w->zone_name);

                                return redirect()->back()->with('success', 'Kích hoạt Website đối tác thành công!');
                            } else {
                                return redirect()->back()->with('error', 'Có lỗi xảy ra vui lòng cấu hình lại Cloudflare1!');
                            }
                        } else {
                            return redirect()->back()->with('error', 'Có lỗi xảy ra vui lòng cấu hình lại Cloudflare2!');
                        }
                    }

                    return redirect()->back()->with('success', 'Kích hoạt Website đối tác thành công!');
                } else {
                    return redirect()->back()->with('error', 'Có lỗi xảy ra vui lòng cấu hình lại Cloudflare!3');
                }
            } else {
                return redirect()->back()->with('error', 'Website đối tác đang chờ cloudflare kích hoạt!');
            }
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra vui lòng cấu hình lại Cloudflare4!');
        }
    }

    public function deletePartnerWebsite($id)
    {
        $w = PartnerWebsite::where('id', $id)->where('is_domain', site('is_domain'))->first();

        $cld = new CloudflareController();
        $cld->deleteDomain($w->zone_id);

        $cpanel = new CpanelController();
        $cpanel->deleteDomain($w->zone_name);

        $w->delete();

        return redirect()->back()->with('success', 'Đã xoá Website đối tác thành công!');
    }
}
