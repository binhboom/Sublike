<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\ServicePlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewServiceController extends Controller
{
    public function viewService(Request $request, $platform, $service)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $status = $request->status;
        $order_code = $request->order_code;

        $platform = ServicePlatform::where('slug', $platform)->where('domain', env('APP_MAIN_SITE'))->where('status', 'active')->first();
        if (!$platform) {
            return abort(404);
        }

        $service = $platform->services()->where('slug', $service)->where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->first();

        if (!$service) {
            return abort(404);
        }

        $orders = $service->orders()->where('user_id', auth()->id())->where('domain', $request->getHost())
            ->when($start_date, function ($query, $start_date) {
                return $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                return $query->where('created_at', '<=', $end_date);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($order_code, function ($query, $order_code) {
                return $query->where('order_code', $order_code)->orWhere('id', $order_code)->orWhere('object_id', $order_code);
            })
            ->orderBy('id', 'desc')->paginate(10);


        $service->servers->map(function ($server) {
            // nếu không có action thì tạo mới
            if (!$server->action) {
                $server->action()->create();
            }
        });

        return view('guard.service.index', compact('platform', 'service', 'orders'));
    }
}
