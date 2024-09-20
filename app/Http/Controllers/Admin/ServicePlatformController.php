<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServicePlatformController extends Controller
{
    public function viewServicePlatform(Request $request)
    {

        $search = $request->search;

        $platforms = ServicePlatform::where('domain', env("APP_MAIN_SITE"))
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            })->orderBy('order', 'asc')->paginate(10);

        return view('admin.service.platform', compact('platforms'));
    }

    public function viewEditServicePlatform($id)
    {
        $platform = ServicePlatform::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();

        if (!$platform) {
            return redirect()->back()->with('error', 'Dịch vụ không tồn tại');
        }

        return view('admin.service.platform-edit', compact('platform'));
    }

    public function updateServicePlatform(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'order' => 'required|integer',
            'name' => 'required|string',
            'slug' => 'required|string',
            'image' => 'required|url',
            'status' => 'required|in:active,inactive'
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $slug = Str::slug($request->slug);

            $checkSlug = ServicePlatform::where('slug', $slug)->where('id', '!=', $id)->first();
            if ($checkSlug) {
                return redirect()->back()->with('error', "Đường dẫn đã tồn tại")->withInput();
            } else {
                $platform = ServicePlatform::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();
                if ($platform) {
                    // kiểm tra order có trung với order khác không
                    $checkOrder = ServicePlatform::where('order', $request->order)->where('id', '!=', $id)->first();
                    if ($checkOrder) {
                        // thay thứ tự đó thành thứ tự của platform cũ
                        $checkOrder->order = $platform->order;
                        $checkOrder->save();
                    }

                    $platform->name = $request->name;
                    $platform->slug = $slug;
                    $platform->image = $request->image;
                    $platform->order = $request->order;
                    $platform->status = $request->status;
                    $platform->save();

                    return redirect()->route('admin.service.platform')->with('success', "Cập nhật thành công");
                } else {
                    return redirect()->back()->with('error', "Không tìm thấy dữ liệu");
                }
            }
        }
    }

    public function createServicePlatform(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required|string',
            'image' => 'required|url',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            $slug = Str::slug($request->slug);

            $checkSlug = ServicePlatform::where('slug', $slug)->first();
            if ($checkSlug) {
                return redirect()->back()->with('error', "Đường dẫn đã tồn tại")->withInput();
            } else {
                // order
                $code = Str::random(8);

                $platform = new ServicePlatform();
                $platform->code = $code;
                $platform->name = $request->name;
                $platform->slug = $slug;
                $platform->image = $request->image;
                $platform->domain = env('APP_MAIN_SITE');
                $platform->order = ServicePlatform::max('order') + 1;
                $platform->save();

                return redirect()->back()->with('success', "Thêm thành công");
            }
        }
    }

    public function deleteServicePlatform($id)
    {
        $platform = ServicePlatform::where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();
        if ($platform) {
            $platform->delete();
            return redirect()->back()->with('success', "Xóa thành công");
        } else {
            return redirect()->back()->with('error', "Không tìm thấy dữ liệu");
        }
    }
}
