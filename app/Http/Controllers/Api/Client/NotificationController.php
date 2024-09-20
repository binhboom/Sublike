<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationSystemResource;
use App\Models\NoticeSystem;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getNotifications(Request $request){
        $notifications = NoticeSystem::orderBy('id', 'desc')->where('domain', $request->getHost())->get();

        return response()->json([
            'status' => 'success',
            'data' => NotificationSystemResource::collection($notifications)
        ]);
    }
}
