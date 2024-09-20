<?php

namespace App\Library;

use Telegram\Bot\BotsManager;

class TelegramSdk
{
    public $bot_token = "";
    public $bot_token_chat = "";

    private $config = [
        'bots' => [
            'bot_notify' => [
                'token' => '',
                'commands' => [
                    \App\Console\Commands\Telegram\Bot\BotNotifyCommand::class
                ]
            ],
            'bot_chat' => [
                'token' => '',
            ],
            'default' => 'bot_notify'
        ]
    ];

    public function __construct()
    {
        // $this->bot_token = $this->config['bots']['bot_notify']['token'];
        $this->config['bots']['bot_notify']['token'] = site('telegram_bot_token') ?? $this->bot_token;
        $this->config['bots']['bot_chat']['token'] = site('telegram_bot_chat_token') ?? $this->bot_token_chat;
    }

    public function thisBot()
    {
        $telegram = new BotsManager($this->config);
        return $telegram;
    }

    public function botNotify($bot_name = 'bot_notify')
    {
        $response = $this->thisBot()->bot($bot_name);
        return $response;
    }

    public function botChat($bot_name = 'bot_chat')
    {
        $response = $this->thisBot()->bot($bot_name);
        return $response;
    }
}
