<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function viewTelegramConfig()
    {
        return view('admin.telegram.config');
    }

    public function setWebhook()
    {
        if (site('telegram_bot_chat_token') !== null || site('telegram_bot_chat_username') !== null) {
            $telegram = new TelegramSdk;
            try{
                $telegram->botChat()->setWebhook([
                    'url' => route('telegram.set-webhook'),
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Set webhook success',
                ]);
            }catch(\Exception $e){
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
            }

        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn chưa cấu hình token cho bot chat',
            ]);
        }
    }
}
