<?php

namespace App\Console\Commands\Telegram\Bot;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class BotNotifyCommand extends Command
{
    protected string $name = 'start';
    protected array $aliases = ['active'];
    protected string $description = 'Start Command to get you started';
    protected string $pattern = '{active: \d+}';


    public function handle()
    {
        $message = $this->getUpdate()->getMessage();
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username ?? 'there';

        $this->replyWithMessage([
            'text' => 'Hello ' . $fallbackUsername . ', welcome to our bot',
            'reply_markup' => Keyboard::make([
                'inline_keyboard' => [
                    [
                        ['text' => 'Xác thực tài khoản', 'callback_data' => 'active'],
                    ],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
                'selective' => false,
            ]),
        ]);

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $this->replyWithMessage([
            'text' => 'You said: ' . $message->text,
        ]);

        $callbackQuery = $this->getUpdate();

        Log::info('Callback Query: ' . json_encode($callbackQuery));

        return response()->json(['status' => 'success']);
    }
}
