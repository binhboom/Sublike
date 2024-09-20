<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePlatform;
use App\Models\SmmPanelPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function viewService(Request $request)
    {

        $search = $request->search;
        $platform_code = $request->platform;

        $services = Service::where('domain', env('APP_MAIN_SITE'))
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            })
            ->when($platform_code, function ($query, $platform_code) {
                return $query->whereHas('platform', function ($query) use ($platform_code) {
                    $query->where('code', $platform_code);
                });
            })
            ->orderBy('id', 'desc')->paginate(10);

        return view('admin.service.service', compact('services'));
    }

    public function viewEditService($id)
    {
        $service = Service::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

        if (!$service) {
            return redirect()->back()->with('error', 'Dịch vụ không tồn tại');
        }

        return view('admin.service.service-edit', compact('service'));
    }

    public function createService(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'platform_id' => 'required|exists:service_platforms,id',
            'name' => 'required|string',
            'slug' => 'required|string',
            'note' => 'required|string',
            'details' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|url',
            'reaction_status' => 'required|in:on,off',
            'quantity_status' => 'required|in:on,off',
            'comments_status' => 'required|in:on,off',
            'minutes_status' => 'required|in:on,off',
            'time_status' => 'required|in:on,off',
            'posts_status' => 'required|in:on,off',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $slugNew = Str::slug($request->slug, '-');
            $package = Str::slug($request->slug, '_');

            $checkSlug = Service::where('slug', $slugNew)->where('domain', env('APP_MAIN_SITE'))->first();

            if ($checkSlug) {
                return redirect()->back()->with('error', 'Đường dẫn đã tồn tại')->withInput();
            } else {

                $code = Str::random(10);

                $order = Service::where('domain', env('APP_MAIN_SITE'))->max('order') + 1;
                $service = new Service();
                $service->platform_id = $request->platform_id;
                $service->name = $request->name;
                $service->slug = $slugNew;
                $service->note = $request->note;
                $service->details = $request->details;
                $service->title = $request->title;
                $service->description = $request->description;
                $service->image = $request->image;
                $service->status = $request->status;
                $service->package = $package;
                $service->code = $code;
                $service->order = $order;
                $service->reaction_status = $request->reaction_status;
                $service->quantity_status = $request->quantity_status;
                $service->comments_status = $request->comments_status;
                $service->minutes_status = $request->minutes_status;
                $service->time_status = $request->time_status;
                $service->posts_status = $request->posts_status;
                $service->domain = env('APP_MAIN_SITE');
                $service->save();

                return redirect()->back()->with('success', 'Tạo dịch vụ thành công');
            }
        }
    }

    public function updateService(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'platform_id' => 'required|exists:service_platforms,id',
            'package' => 'required|string', // add 'package' => 'required|string
            'name' => 'required|string',
            'slug' => 'required|string',
            'note' => 'required|string',
            'details' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|url',
            'reaction_status' => 'required|in:on,off',
            'quantity_status' => 'required|in:on,off',
            'comments_status' => 'required|in:on,off',
            'minutes_status' => 'required|in:on,off',
            'time_status' => 'required|in:on,off',
            'posts_status' => 'required|in:on,off',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $slugNew = Str::slug($request->slug, '-');
            $package = Str::slug($request->package, '_');

            $checkSlug = Service::where('slug', $slugNew)->where('id', '!=', $id)->where('domain', env('APP_MAIN_SITE'))->first();
            $checkPackage = Service::where('package', $package)->where('id', '!=', $id)->where('domain', env('APP_MAIN_SITE'))->first();

            if ($checkSlug) {
                return redirect()->back()->with('error', 'Đường dẫn đã tồn tại')->withInput();
            }

            if ($checkPackage) {
                return redirect()->back()->with('error', 'Gói đã tồn tại')->withInput();
            }

            $service = Service::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

            if ($service) {
                $service->platform_id = $request->platform_id;
                $service->name = $request->name;
                $service->slug = $slugNew;
                $service->note = $request->note;
                $service->details = $request->details;
                $service->title = $request->title;
                $service->description = $request->description;
                $service->image = $request->image;
                $service->status = $request->status;
                $service->reaction_status = $request->reaction_status;
                $service->quantity_status = $request->quantity_status;
                $service->comments_status = $request->comments_status;
                $service->minutes_status = $request->minutes_status;
                $service->time_status = $request->time_status;
                $service->posts_status = $request->posts_status;
                $service->package = $package;
                $service->save();

                return redirect()->back()->with('success', 'Cập nhật dịch vụ thành công');
            } else {
                return redirect()->back()->with('error', 'Dịch vụ không tồn tại');
            }
        }
    }

    public function viewSmmService(Request $request)
    {

        $smmlist = SmmPanelPartner::where('domain', env('APP_MAIN_SITE'))->orderBy('id', 'desc')->paginate(10);

        return view('admin.service.smm-service', compact('smmlist'));
    }

    public function createSmmService(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'api_key' => 'required|string',
            'url_api' => 'required|url',
            'status' => 'required|in:on,off',
            'update_price' => 'required|in:on,off',
            'price_update' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $checkSlug = SmmPanelPartner::where('name', $request->name)->where('domain', env('APP_MAIN_SITE'))->first();

            if ($checkSlug) {
                return redirect()->back()->with('error', 'Đối tác đã tồn tại')->withInput();
            } else {

                $smm = new SmmPanelPartner();
                $smm->name = $request->name;
                $smm->api_token = $request->api_key;
                $smm->url_api = $request->url_api;
                $smm->status = $request->status;
                $smm->update_price = $request->update_price;
                $smm->price_update = $request->price_update;
                $smm->domain = env('APP_MAIN_SITE');
                $smm->save();

                return redirect()->back()->with('success', 'Tạo đối tác thành công');
            }
        }
    }

    public function viewEditSmmService($id)
    {
        $smm = SmmPanelPartner::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

        if (!$smm) {
            return redirect()->back()->with('error', 'Đối tác không tồn tại');
        }

        return view('admin.service.smm-service-edit', compact('smm'));
    }

    public function updateSmmService(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'api_key' => 'required|string',
            'url_api' => 'required|url',
            'status' => 'required|in:on,off',
            'update_price' => 'required|in:on,off',
            'price_update' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $smm = SmmPanelPartner::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

            if ($smm) {
                $smm->name = $request->name;
                $smm->api_token = $request->api_key;
                $smm->url_api = $request->url_api;
                $smm->status = $request->status;
                $smm->update_price = $request->update_price;
                $smm->price_update = $request->price_update;
                $smm->save();

                return redirect()->back()->with('success', 'Cập nhật đối tác thành công');
            } else {
                return redirect()->back()->with('error', 'Đối tác không tồn tại');
            }
        }
    }

    public function deleteSmmService($id)
    {
        $smm = SmmPanelPartner::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

        if ($smm) {
            $smm->delete();
            return redirect()->back()->with('success', 'Xóa đối tác thành công');
        } else {
            return redirect()->back()->with('error', 'Đối tác không tồn tại');
        }
    }
}
