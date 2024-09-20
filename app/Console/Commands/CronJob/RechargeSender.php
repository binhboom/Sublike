<?php

namespace App\Console\Commands\CronJob;

use App\Library\TelegramSdk;
use App\Models\Recharge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RechargeSender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:recharge-notification {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo nạp tiền cho Admin Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $domain = $this->option('domain');
        Log::info('🚀 Bắt đầu gửi thông báo nạp tiền cho Admin Telegram 🚀 ' . $domain);
        $recharges = Recharge::where('domain', $domain)->where('is_send_telegram', false)->limit(10)->get();
        if ($recharges->count() > 0) {
            $bot_notify = new TelegramSdk();
            foreach ($recharges as $recharge) {
                $user = $recharge->user;
                if (siteValue('telegram_bot_token', $domain) && siteValue('telegram_chat_id', $domain)) {
                    $bot_notify->botNotify()->sendMessage([
                        'chat_id' => siteValue('telegram_chat_id', $domain),
                        'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                            '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                            '💰 <b>Số tiền:</b> ' . number_format($recharge->real_amount) . ' VNĐ' . PHP_EOL .
                            '🏦 <b>Loại Bank:</b> ' . $recharge->payment_method . PHP_EOL .
                            '📝 <b>Ghi chú:</b> ' . $recharge->note . PHP_EOL .
                            '🔗 <b>Mã giao dịch:</b> ' . $recharge->order_code . PHP_EOL .
                            '📅 <b>Thời gian:</b> ' . $recharge->created_at->format('d/m/Y H:i:s') . PHP_EOL .
                            '🌐 <b>Domain:</b> ' . $domain,
                        'parse_mode' => 'HTML',
                    ]);
                    $recharge->is_send_telegram = true;
                    $recharge->save();
                }
            }
        }
    }
}
