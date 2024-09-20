<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramController extends Controller
{
    public function callbackData(Request $request)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $bot = new TelegramSdk();

        if (isset($input['message'])) {
            $message = $input['message'];
            $chat_id = $message['chat']['id'];
            $text = $message['text'];
            $username  = $message['chat']['username'];

            if ($text === '/start') {
                $bot->botChat()->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => 'Xin chào bạn: ' . $username . ' tôi là Bot của Website ' . getDomain() . '. Bạn vui lòng chọn thao tác bên dưới',
                    'reply_markup' => Keyboard::make([
                        'inline_keyboard' => [
                            [
                                ['text' => 'Xác thực tài khoản', 'callback_data' => 'verify_account'],
                            ],
                        ],
                        'resize_keyboard' => true,
                        'one_time_keyboard' => false,
                        'selective' => false,
                    ]),
                ]);
            }

            if (strpos($text, '/active') !== false) {
                $token = str_replace('/active ', '', $text);
                $token = str_replace(' ', '', $token);
                $token = str_replace("\n", '', $token);
                $token = str_replace("\r", '', $token);
                $user = User::where('api_token', $token)->first();
                // checkid chat có tồn tại trong hệ thống hay không
                $user_check = User::where('telegram_id', $chat_id)->first();
                if ($user_check) {
                    $bot->botChat()->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Tài khoản của bạn đã được xác thực trước đó. Bạn vui lòng kiểm tra lại",
                    ]);
                    return;
                } else {
                    if ($user) {
                        $user->update([
                            'telegram_id' => $chat_id,
                            'telegram_link' => "https://t.me/$username",
                        ]);
                        $bot->botChat()->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => "Xác thực tài khoản thành công bản ơn bạn {$user->name} đã sử dụng dịch vụ của chúng tôi.",
                        ]);
                    } else {
                        $bot->botChat()->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => "Token không hợp lệ. Bạn vui lòng kiểm tra lại",
                        ]);
                    }
                }
            }
        }

        if (isset($input['callback_query'])) {
            $callback_query = $input['callback_query'];
            $chat_id = $callback_query['message']['chat']['id'];
            $data = $callback_query['data'];

            if ($data == 'verify_account') {

                $bot->botChat()->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => "Nhập api token của bạn sử dụng lện /active <token>",
                ]);
            }
        }
    }
}
