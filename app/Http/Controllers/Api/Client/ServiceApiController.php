<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PlatformApiResouce;
use App\Http\Resources\Api\ServerApiResource;
use App\Http\Resources\Api\ServiceApiResource;
use App\Models\Service;
use App\Models\ServicePlatform;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function getServices(Request $request)
    {
        $group_by = $request->group_by ?? 'platform';

        $platforms = ServicePlatform::where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy danh sách dịch vụ thành công",
            'data' => PlatformApiResouce::collection($platforms)
        ], 201);
    }

    public function getService(Request $request, $platform, $service)
    {
        $platform = ServicePlatform::where('slug', $platform)->where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->first();
        $service = Service::where('slug', $service)->where('status', 'active')->where('platform_id', $platform->id)->first();

        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy dịch vụ"
            ]);
        }


        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin dịch vụ thành công",
            'data' => [
                'platform' => $platform,
                'service' => $service,
            ]
        ], 201);
    }

    public function getServers(Request $request)
    {
        $platform = $request->platform;
        $service = $request->service;
        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy máy chủ"
            ]);
        }

        $platform = ServicePlatform::where('slug', $platform)->where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->first();
        $service = Service::where('slug', $service)->where('status', 'active')->where('platform_id', $platform->id)->first();

        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy máy chủ"
            ]);
        }

        $servers = $service->servers()->where('domain', $request->getHost())->get();

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy danh sách máy chủ thành công",
            'data' => ServerApiResource::collection($servers)
        ], 201);
    }

    public function getTotalPaymentServer(Request $request)
    {
        $platform = $request->platform;
        $service = $request->service;

        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy máy chủ"
            ]);
        }

        $platform = ServicePlatform::where('slug', $platform)->where('status', 'active')->where('domain', env('APP_MAIN_SITE'))->first();
        $service = Service::where('slug', $service)->where('status', 'active')->where('platform_id', $platform->id)->first();

        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy máy chủ"
            ]);
        }

        $quantity = $request->quantity ?? 0;
        $minutes = $request->minutes ?? 0;
        $posts = $request->posts ?? 0;
        $duration = $request->duration ?? 0;
        $comment = $request->comment ?? '';

        $server = $service->servers()->where('domain', $request->getHost())->where('package_id', $request->package_id)->first();
        if (!$server) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => "Không tìm thấy máy chủ"
            ]);
        }

        $price = $server->levelPrice(null);
        $action = $server->action;
        $total = 0;

        if ($action) {
            if ($action->quantity_status == 'on' && $quantity > 0) {
                $total += $price * $quantity;
            }

            if ($action->minutes_status == 'on' && $minutes > 0) {
                $total += $total * $minutes;
            }

            if ($action->posts_status == 'on' && $posts > 0) {
                $total += $total * $posts;
            }

            if ($action->time_status == 'on' && $duration > 0) {
                $total += $total * $duration;
            }

            if ($action->comment_status == 'on' && $comment) {
                $comments = explode("\n", $comment);
                $comments = array_filter($comments, function ($line) {
                    return trim($line) !== '';
                });
                $total += $total * count($comments);
            }
        }

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Tính giá thành công",
            'data' => [
                'total' => $total
            ]
        ], 201);
    }
}
