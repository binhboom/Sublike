<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoticeService;
use App\Models\NoticeSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function viewSystemNotify(Request $request)
    {

        $noticeSystems = NoticeSystem::where('domain', $request->getHost())->orderBy('id', 'desc')->paginate(10);

        return view('admin.notification.system', compact('noticeSystems'));
    }

    public function createSystemNotify(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string|max:255',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $noticeSystem = new NoticeSystem();
            $noticeSystem->user_id = auth()->id();
            $noticeSystem->title = $request->title;
            $noticeSystem->content = $request->content;
            $noticeSystem->color = $request->color;
            $noticeSystem->domain = $request->getHost();
            $noticeSystem->save();

            return redirect()->back()->with('success', 'Thêm thông báo thành công');
        }
    }

    public function deleteSystemNotify($id)
    {
        $noticeSystem = NoticeSystem::where('id', $id)->where('domain', request()->getHost())->first();
        if ($noticeSystem) {
            $noticeSystem->delete();
            return redirect()->back()->with('success', 'Xóa thông báo thành công');
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy thông báo');
        }
    }

    //
    public function viewServiceNotify(Request $request)
    {
        $noticeServices = NoticeService::where('domain', $request->getHost())->orderBy('id', 'desc')->paginate(10);

        return view('admin.notification.service', compact('noticeServices'));
    }

    public function createServiceNotify(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string|max:255',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $noticeService = new NoticeService();
            $noticeService->user_id = auth()->id();
            $noticeService->title = $request->title;
            $noticeService->content = $request->content;
            $noticeService->color = $request->color;
            $noticeService->domain = $request->getHost();
            $noticeService->save();

            return redirect()->back()->with('success', 'Thêm thông báo thành công');
        }
    }

    public function deleteServiceNotify($id)
    {
        $noticeService = NoticeService::where('id', $id)->where('domain', request()->getHost())->first();
        if ($noticeService) {
            $noticeService->delete();
            return redirect()->back()->with('success', 'Xóa thông báo thành công');
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy thông báo');
        }
    }
}
