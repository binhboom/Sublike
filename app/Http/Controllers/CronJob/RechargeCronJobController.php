<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\Banking;
use App\Models\Recharge;
use App\Models\RechargeCard;
use App\Models\RechargePromotion;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class RechargeCronJobController extends Controller
{

    public function rechargeCardStatus(Request $request)
    {

        $listRechargeCard = RechargeCard::where('status', 'pending')->get();
        // print_r($listRechargeCard);
        foreach ($listRechargeCard as $list) {
            $partner_id = site('partner_id');
            $partner_key = site('partner_key');
            $percent_card = site('percent_card');

            $sign = md5($partner_key . $list->pin . $list->serial);

            $statusCard = thecao($list->type, $list->pin, $list->serial, $list->amount, $list->code, $partner_id, $sign, 'check');
            // print_r($statusCard);
            if (isset($statusCard->status)) {
                if ($statusCard->status == 1) {
                    $user = $list->user;
                    $chietkhau = $list->amount - ($list->amount * $percent_card / 100);
                    $note = "Bạn đã nạp thành công " . number_format($list->amount) . " VNĐ từ thẻ cào. Số dư tài khoản của bạn là " . number_format($user->balance + $chietkhau) . " VNĐ";
                    Transaction::create([
                        'user_id' => $user->id,
                        'tran_code' => $list->code,
                        'type' => 'recharge',
                        'action' => 'add',
                        'first_balance' => $list->amount,
                        'before_balance' => $user->balance,
                        'after_balance' => $user->balance + $chietkhau,
                        'note' => $note,
                        'ip' => $request->ip(),
                        'domain' => $user->domain
                    ]);

                    $list->status = 'success';
                    $list->save();
                    $user->balance = $user->balance + $chietkhau;
                    $user->total_recharge = $user->total_recharge + $list->amount;
                    $user->save();
                } elseif ($statusCard->status == 3 || $statusCard->status == 2) {
                    $list->status = 'error';
                    $list->save();
                }
            }
        }

        // $status = $request->status; // 1: Thành công, 2: Thẻ thành công sai mệnh giá,3: Thẻ lỗi,99: Thẻ chờ xử lý
        // $message = $request->message;
        // $request_id = $request->request_id;
        // $declared_value = $request->declared_value;
        // $value = $request->value;
        // $amount = $request->amount;
        // $code = $request->code;
        // $serial = $request->serial;
        // $telco = $request->telco;
        // $trans_id = $request->trans_id;
        // $callback_sign = $request->callback_sign;

        // if ($status || $message || $request_id || $declared_value || $value || $amount || $code || $serial || $telco || $trans_id || $callback_sign) {

        //     $partner_id = site('partner_id');
        //     $partner_key = site('partner_key');
        //     $percent_card = site('percent_card');

        //     $sign = md5($partner_key . $code . $serial);

        //     if ($sign == $callback_sign && $status == 1) {

        //         $rechargeCard = RechargeCard::where('trans_id', $trans_id)->where('code', $request->request_id)->first();
        //         if ($rechargeCard) {
        //             $user = $rechargeCard->user;
        //             $chietkhau = $value - ($value * $percent_card / 100);
        //             $note = "Bạn đã nạp thành công " . number_format($value) . " VNĐ từ thẻ cào. Số dư tài khoản của bạn là " . number_format($user->balance + $chietkhau) . " VNĐ";
        //             Transaction::create([
        //                 'user_id' => $user->id,
        //                 'tran_code' => $trans_id,
        //                 'type' => 'recharge',
        //                 'action' => 'add',
        //                 'first_balance' => $value,
        //                 'before_balance' => $user->balance,
        //                 'after_balance' => $user->balance + $chietkhau,
        //                 'note' => $note,
        //                 'ip' => $request->ip(),
        //                 'domain' => $user->domain
        //             ]);
        //             $rechargeCard->status = 'success';
        //             $rechargeCard->save();
        //             $user->balance = $user->balance + $chietkhau;
        //             $user->total_recharge = $user->total_recharge + $value;
        //             $user->save();
        //         }
        //     }
        // }
    }

    public function payment(Request $request, $code)
    {

        if ($code === 'Momo') {
            $momo = Banking::where('domain', $request->getHost())->where('bank_name', 'Momo')->first();

            if ($momo) {
                $token = $momo->token;
                $min_recharge = $momo->min_recharge;

                $transfer_code = strtolower(siteValue('transfer_code'));

                $ch = curl_init('https://api.web2m.com/historyapimomo1h/' . $token);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);
                // print_r($result);

                if (isset($result['momoMsg']['tranList'])) {
                    foreach ($result['momoMsg']['tranList'] as $key => $item) {
                        $partnerName = $item['partnerName'];
                        $partnerId = $item['partnerId'];
                        $amount = $item['amount'];
                        $comment = strtolower($item['comment']);
                        $tranId = $item['tranId'];
                        if ($amount < $min_recharge) {
                            continue;
                        }
                        // echo $comment;
                        $checkId = strpos($comment, $transfer_code);
                        if ($checkId !== false) {
                            $ch1 = explode($transfer_code, $comment);
                            $ch1 = $ch1[1];
                            $ch1 = str_replace("\n", "", $ch1);
                            $ch2 = explode('.', $ch1);
                            $ch1 = $ch2[0];
                            $ch2 = explode(' ', $ch1);
                            $idUser = $ch2[0];
                            //name bank
                            $name = "Không xác định";
                            $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                            if (!$user) {
                                continue;
                            }
                            $tranId = str_replace('-', '', $tranId);
                            $checkTransaction = Recharge::where('bank_code', $tranId)->first();
                            if ($checkTransaction) {
                                // echo $idUser;
                                continue;
                            } else {
                                $balance = $user->balance;
                                $total_recharge = $user->total_recharge;

                                $percent_promotion = siteValue('percent_promotion');
                                $start_promotion = siteValue('start_promotion');
                                $end_promotion = siteValue('end_promotion');

                                $promotion = 0;

                                $note = "Bạn đã nạp thành công " . number_format($amount) . " VNĐ từ Momo. Số dư tài khoản của bạn là " . number_format($balance + $amount) . " VNĐ";
                                $amountBefore = $amount;
                                if ($percent_promotion > 0) {
                                    //2024-03-28
                                    $start = Carbon::parse($start_promotion);
                                    $end = Carbon::parse($end_promotion);
                                    $now = Carbon::now();
                                    if ($now->between($start, $end)) {
                                        $promotion = $amount * $percent_promotion / 100;
                                        $amount = $amount + $promotion;
                                        $note = "Bạn đã nạp thành công " . number_format($amount) . " VNĐ từ Momo. Số dư tài khoản của bạn là " . number_format($balance + $amount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                    }
                                }

                                $promotion = RechargePromotion::where('min_balance', '<=', $amount)->where('status', 'active')->where('domain', request()->getHost())->first();

                                if ($promotion && $promotion->min_balance <= $amount) {
                                    $promotion_amount = $amount * $promotion->percentage / 100;
                                    $amount = $amount + $promotion_amount;
                                    $note = "Bạn đã nạp thành công " . number_format($amount) . " VNĐ từ Momo. Số dư tài khoản của bạn là " . number_format($balance + $amount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                }

                                if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                    $bot_notify = new TelegramSdk();
                                    $bot_notify->botNotify()->sendMessage([
                                        'chat_id' => siteValue('telegram_chat_id'),
                                        'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                            '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                            '💰 <b>Số tiền:</b> ' . number_format($amount) . ' VNĐ' . PHP_EOL .
                                            '🏦 <b>Loại Bank:</b> ' . "Momo" . PHP_EOL .
                                            '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                            '🔗 <b>Mã giao dịch:</b> ' . $tranId . PHP_EOL .
                                            '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                            '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                            '🌐 <b>Domain:</b> ' . $request->getHost(),
                                        'parse_mode' => 'HTML',
                                    ]);
                                }

                                Transaction::create([
                                    'user_id' => $idUser,
                                    'tran_code' => $tranId,
                                    'type' => 'recharge',
                                    'action' => 'add',
                                    'first_balance' => $amount,
                                    'before_balance' => $balance,
                                    'after_balance' => $balance + $amount,
                                    'note' => $note,
                                    'ip' => $request->ip(),
                                    'domain' => $user->domain
                                ]);

                                Recharge::create([
                                    'user_id' => $idUser,
                                    'order_code' => $tranId,
                                    'bank_code' => $tranId,
                                    'payment_method' => 'Momo',
                                    'bank_name' => $partnerName ?? "Không xác định",
                                    'amount' => $amountBefore,
                                    'real_amount' => $amount,
                                    'status' => 'Success',
                                    'note' => $note,

                                    'domain' => $user->domain
                                ]);

                                $user->balance = $balance + $amount;
                                $user->total_recharge = $total_recharge + $amount;
                                $user->save();

                                // $telegram = new TelegramSdk();

                            }
                        }
                    }
                }
            }
        }

        if ($code === 'Mbbank') {
            $mbbank = Banking::where('domain', $request->getHost())->where('bank_name', 'MBBank')->first();

            if ($mbbank) {
                $api_token = $mbbank->token;
                $code_tranfer = strtolower(siteValue('transfer_code'));
                $min_recharge = $mbbank->min_recharge;


                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.vpnfast.vn/api/historymbbank/' . $api_token,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $result = json_decode($response, true);
                // print_r($result);

                $count = 0;
                if (isset($result['TranList'])) {
                    foreach ($result['TranList'] as $key => $item) {
                        $refNo = $item['refNo'];
                        $description = strtolower($item['description']);
                        $creditAmount = $item['creditAmount'];
                        $debitAmount = $item['debitAmount'];
                        $description1 = str_replace(" ", " ", $description);
                        if ($creditAmount >= $min_recharge) {
                            $checkId = strpos($description1, $code_tranfer);
                            if ($checkId !== false) {
                                $ch1 = explode($code_tranfer, $description1);
                                $ch1 = $ch1[1];
                                $ch1 = str_replace("\n", "", $ch1);
                                $ch2 = explode('.', $ch1);
                                $ch1 = $ch2[0];
                                $ch2 = explode(' ', $ch1);
                                $idUser = $ch2[0];

                                $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                if ($user) {
                                    $refNo = base64_encode($refNo);
                                    $checkTransaction = Recharge::where('bank_code', $refNo)->first();
                                    if ($checkTransaction) {
                                        continue;
                                    } else {
                                        $balance = $user->balance;
                                        $total_recharge = $user->total_recharge;

                                        $percent_promotion = siteValue('percent_promotion');
                                        $start_promotion = siteValue('start_promotion');
                                        $end_promotion = siteValue('end_promotion');

                                        $promotion = 0;

                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                        $amountBefore = $creditAmount;
                                        if ($percent_promotion > 0) {
                                            //2024-03-28
                                            $start = Carbon::parse($start_promotion);
                                            $end = Carbon::parse($end_promotion);
                                            $now = Carbon::now();
                                            if ($now->between($start, $end)) {
                                                $promotion = $creditAmount * $percent_promotion / 100;
                                                $creditAmount = $creditAmount + $promotion;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                            }
                                        }

                                        $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                        if ($promotion && $creditAmount <= $promotion->min_balance) {
                                            $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                            $creditAmount = $creditAmount + $promotion_amount;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                        }

                                        /* telegra */
                                        if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                            $bot_notify = new TelegramSdk();
                                            $bot_notify->botNotify()->sendMessage([
                                                'chat_id' => siteValue('telegram_chat_id'),
                                                'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                    '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                    '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                    '🏦 <b>Loại Bank:</b> ' . "MBBank" . PHP_EOL .
                                                    '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                    '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                    '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                    '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                    '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                'parse_mode' => 'HTML',
                                            ]);
                                        }

                                        Transaction::create([
                                            'user_id' => $idUser,
                                            'tran_code' => $refNo,
                                            'type' => 'recharge',
                                            'action' => 'add',
                                            'first_balance' => $creditAmount,
                                            'before_balance' => $balance,
                                            'after_balance' => $balance + $creditAmount,
                                            'note' => $note,
                                            'ip' => $request->ip(),
                                            'domain' => $user->domain
                                        ]);

                                        Recharge::create([
                                            'user_id' => $idUser,
                                            'order_code' => $refNo,
                                            'bank_code' => $refNo,
                                            'payment_method' => 'MBBank',
                                            'bank_name' => 'MBBank',
                                            'amount' => $amountBefore,
                                            'real_amount' => $creditAmount,
                                            'status' => 'Success',
                                            'note' => $note,
                                            'domain' => $user->domain
                                        ]);

                                        $user->balance = $balance + $creditAmount;
                                        $user->total_recharge = $total_recharge + $creditAmount;
                                        $user->save();
                                    }
                                } else {
                                    $checkTransaction = Recharge::where('order_code', $idUser)->where('type', 'bill')->where('status', '=', 'Pending')->first();
                                    if ($checkTransaction) {
                                        $user = User::find($checkTransaction->user_id);
                                        if (!$user) {
                                            continue;
                                        }
                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            $balance = $user->balance;
                                            $total_recharge = $user->total_recharge;
                                            $percent_promotion = siteValue('percent_promotion');
                                            $start_promotion = siteValue('start_promotion');
                                            $end_promotion = siteValue('end_promotion');
                                            $promotion = 0;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                            $amountBefore = $creditAmount;
                                            if ($percent_promotion > 0) {
                                                //2024-03-28
                                                $start = Carbon::parse($start_promotion);
                                                $end = Carbon::parse($end_promotion);
                                                $now = Carbon::now();
                                                if ($now->between($start, $end)) {
                                                    $promotion = $creditAmount * $percent_promotion / 100;
                                                    $creditAmount = $creditAmount + $promotion;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                }
                                            }

                                            $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                            if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                $creditAmount = $creditAmount + $promotion_amount;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                            }

                                            /* telegra */
                                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                $bot_notify = new TelegramSdk();
                                                $bot_notify->botNotify()->sendMessage([
                                                    'chat_id' => siteValue('telegram_chat_id'),
                                                    'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                        '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                        '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                        '🏦 <b>Loại Bank:</b> ' . "MBBank" . PHP_EOL .
                                                        '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                        '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                        '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                        '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                        '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                    'parse_mode' => 'HTML',
                                                ]);
                                            }
                                            Transaction::create([
                                                'user_id' => $user->id,
                                                'tran_code' => $refNo,
                                                'type' => 'recharge',
                                                'action' => 'add',
                                                'first_balance' => $creditAmount,
                                                'before_balance' => $balance,
                                                'after_balance' => $balance + $creditAmount,
                                                'note' => $note,
                                                'ip' => $request->ip(),
                                                'domain' => $user->domain
                                            ]);
                                            $checkTransaction->bank_code = $refNo;
                                            $checkTransaction->real_amount = $creditAmount;
                                            $checkTransaction->status = 'Success';
                                            $checkTransaction->note = $note;
                                            $checkTransaction->paid_at = now();
                                            $checkTransaction->save();
                                            $user->balance = $balance + $creditAmount;
                                            $user->total_recharge = $total_recharge + $creditAmount;
                                            $user->save();
                                        }
                                    }
                                }
                            } else {
                                $bills = Recharge::where('type', 'bill')->where('status', 'Pending')->where('expired_at', '>', now())->limit(100)->get();
                                // print_r($bills);
                                foreach ($bills as $bill) {
                                    $order_code = $bill->order_code;

                                    $checkBill = strpos($description1, $order_code);
                                    if ($checkBill !== false) {
                                        $idUser = $bill->user_id;
                                        $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                        if (!$user) {
                                            continue;
                                        }

                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            if ($checkTransaction2) {
                                                continue;
                                            } else {
                                                $balance = $user->balance;
                                                $total_recharge = $user->total_recharge;

                                                $percent_promotion = siteValue('percent_promotion');
                                                $start_promotion = siteValue('start_promotion');
                                                $end_promotion = siteValue('end_promotion');

                                                $promotion = 0;

                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                                $amountBefore = $creditAmount;
                                                if ($percent_promotion > 0) {
                                                    //2024-03-28
                                                    $start = Carbon::parse($start_promotion);
                                                    $end = Carbon::parse($end_promotion);
                                                    $now = Carbon::now();
                                                    if ($now->between($start, $end)) {
                                                        $promotion = $creditAmount * $percent_promotion / 100;
                                                        $creditAmount = $creditAmount + $promotion;
                                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                    }
                                                }

                                                $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                                if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                    $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                    $creditAmount = $creditAmount + $promotion_amount;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ MBBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                                }

                                                /* telegra */
                                                if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                    $bot_notify = new TelegramSdk();
                                                    $bot_notify->botNotify()->sendMessage([
                                                        'chat_id' => siteValue('telegram_chat_id'),
                                                        'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                            '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                            '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                            '🏦 <b>Loại Bank:</b> ' . "MBBank" . PHP_EOL .
                                                            '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                            '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                            '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                            '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                            '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                        'parse_mode' => 'HTML',
                                                    ]);
                                                }

                                                Transaction::create([
                                                    'user_id' => $idUser,
                                                    'tran_code' => $refNo,
                                                    'type' => 'recharge',
                                                    'action' => 'add',
                                                    'first_balance' => $creditAmount,
                                                    'before_balance' => $balance,
                                                    'after_balance' => $balance + $creditAmount,
                                                    'note' => $note,
                                                    'ip' => $request->ip(),
                                                    'domain' => $user->domain
                                                ]);

                                                $bill->bank_code = $refNo;
                                                $bill->real_amount = $creditAmount;
                                                $bill->status = 'Success';
                                                $bill->note = $note;
                                                $bill->paid_at = now();
                                                $bill->save();

                                                $user->balance = $balance + $creditAmount;
                                                $user->total_recharge = $total_recharge + $creditAmount;
                                                $user->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($code === 'techcombank') {

            $techcombank = Banking::where('domain', $request->getHost())->where('bank_name', 'Techcombank')->first();

            if ($techcombank) {
                $api_token = $techcombank->token;
                $code_tranfer = strtolower(siteValue('transfer_code'));

                $ch = curl_init('https://api.vpnfast.vn/api/historytechcombank/' . $api_token);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                // dd($response);
                $result = json_decode($response, true);
                $count = 0;
                if (isset($result['transactions'])) {
                    foreach ($result['transactions'] as $key => $item) {
                        $refNo = $item['Reference'];
                        $description = strtolower($item['Description']);
                        // $description = $item['Description'];
                        $creditAmount = str_replace(",", "", $item['Amount']);
                        $description1 = str_replace(" ", "", $description);
                        if ($creditAmount >= $techcombank->min_recharge && $item['CD'] == '+') {
                            $checkId = strpos($description1, $code_tranfer);
                            if ($checkId !== false) {
                                $ch1 = explode($code_tranfer, $description1);
                                $ch1 = $ch1[1];
                                $ch1 = str_replace("\n", "", $ch1);
                                $ch2 = explode('.', $ch1);
                                $ch1 = $ch2[0];
                                $ch2 = explode(' ', $ch1);
                                $idUser = $ch2[0];

                                // $user = User::where('id', $idUser)->where('domain', $request->getHost())->first();
                                $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                if ($user) {
                                    $refNo = base64_encode($refNo);
                                    $checkTransaction = Recharge::where('bank_code', $refNo)->first();

                                    if ($checkTransaction) {
                                        continue;
                                    } else {

                                        $balance = $user->balance;
                                        $total_recharge = $user->total_recharge;

                                        $percent_promotion = siteValue('percent_promotion');
                                        $start_promotion = siteValue('start_promotion');
                                        $end_promotion = siteValue('end_promotion');

                                        $promotion = 0;

                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                        $amountBefore = $creditAmount;
                                        if ($percent_promotion > 0) {
                                            //2024-03-28
                                            $start = Carbon::parse($start_promotion);
                                            $end = Carbon::parse($end_promotion);
                                            $now = Carbon::now();
                                            if ($now->between($start, $end)) {
                                                $promotion = $creditAmount * $percent_promotion / 100;
                                                $creditAmount = $creditAmount + $promotion;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                            }
                                        }

                                        $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                        if ($promotion && $creditAmount <= $promotion->min_balance) {
                                            $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                            $creditAmount = $creditAmount + $promotion_amount;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                        }

                                        /* telegra */
                                        if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                            $bot_notify = new TelegramSdk();
                                            $bot_notify->botNotify()->sendMessage([
                                                'chat_id' => siteValue('telegram_chat_id'),
                                                'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                    '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                    '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                    '🏦 <b>Loại Bank:</b> ' . "Techcombank" . PHP_EOL .
                                                    '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                    '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                    '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                    '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                    '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                'parse_mode' => 'HTML',
                                            ]);
                                        }

                                        Transaction::create([
                                            'user_id' => $idUser,
                                            'tran_code' => $refNo,
                                            'type' => 'recharge',
                                            'action' => 'add',
                                            'first_balance' => $creditAmount,
                                            'before_balance' => $balance,
                                            'after_balance' => $balance + $creditAmount,
                                            'note' => $note,
                                            'ip' => $request->ip(),
                                            'domain' => $user->domain
                                        ]);


                                        Recharge::create([
                                            'user_id' => $idUser,
                                            'order_code' => $refNo,
                                            'bank_code' => $refNo,
                                            'payment_method' => 'Techcombank',
                                            'bank_name' => 'Techcombank',
                                            'amount' => $amountBefore,
                                            'real_amount' => $creditAmount,
                                            'status' => 'Success',
                                            'note' => $note,
                                            'domain' => $user->domain
                                        ]);

                                        $user->balance = $balance + $creditAmount;
                                        $user->total_recharge = $total_recharge + $creditAmount;
                                        $user->save();
                                        echo "done";
                                    }
                                } else {
                                    $checkTransaction = Recharge::where('order_code', $idUser)->where('type', 'bill')->where('status', '=', 'Pending')->first();
                                    if ($checkTransaction) {
                                        $user = User::find($checkTransaction->user_id);
                                        if (!$user) {
                                            continue;
                                        }
                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            $balance = $user->balance;
                                            $total_recharge = $user->total_recharge;
                                            $percent_promotion = siteValue('percent_promotion');
                                            $start_promotion = siteValue('start_promotion');
                                            $end_promotion = siteValue('end_promotion');
                                            $promotion = 0;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                            $amountBefore = $creditAmount;
                                            if ($percent_promotion > 0) {
                                                //2024-03-28
                                                $start = Carbon::parse($start_promotion);
                                                $end = Carbon::parse($end_promotion);
                                                $now = Carbon::now();
                                                if ($now->between($start, $end)) {
                                                    $promotion = $creditAmount * $percent_promotion / 100;
                                                    $creditAmount = $creditAmount + $promotion;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                }
                                            }

                                            $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                            if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                $creditAmount = $creditAmount + $promotion_amount;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                            }

                                            /* telegra */
                                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                $bot_notify = new TelegramSdk();
                                                $bot_notify->botNotify()->sendMessage([
                                                    'chat_id' => siteValue('telegram_chat_id'),
                                                    'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                        '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                        '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                        '🏦 <b>Loại Bank:</b> ' . "Techcombank" . PHP_EOL .
                                                        '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                        '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                        '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                        '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                        '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                    'parse_mode' => 'HTML',
                                                ]);
                                            }

                                            Transaction::create([
                                                'user_id' => $user->id,
                                                'tran_code' => $refNo,
                                                'type' => 'recharge',
                                                'action' => 'add',
                                                'first_balance' => $creditAmount,
                                                'before_balance' => $balance,
                                                'after_balance' => $balance + $creditAmount,
                                                'note' => $note,
                                                'ip' => $request->ip(),
                                                'domain' => $user->domain
                                            ]);

                                            $checkTransaction->bank_code = $refNo;
                                            $checkTransaction->real_amount = $creditAmount;
                                            $checkTransaction->status = 'Success';
                                            $checkTransaction->note = $note;
                                            $checkTransaction->paid_at = now();
                                            $checkTransaction->save();
                                            $user->balance = $balance + $creditAmount;
                                            $user->total_recharge = $total_recharge + $creditAmount;
                                            $user->save();
                                        }
                                    }
                                }
                            } else {
                                $bills = Recharge::where('type', 'bill')->where('status', 'Pending')->where('expired_at', '>', now())->limit(100)->get();

                                foreach ($bills as $bill) {
                                    $order_code = $bill->order_code;

                                    $checkBill = strpos($description, $order_code);
                                    if ($checkBill !== false) {
                                        $idUser = $bill->user_id;
                                        $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                        if (!$user) {
                                            continue;
                                        }

                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            $balance = $user->balance;
                                            $total_recharge = $user->total_recharge;

                                            $percent_promotion = siteValue('percent_promotion');
                                            $start_promotion = siteValue('start_promotion');
                                            $end_promotion = siteValue('end_promotion');

                                            $promotion = 0;

                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                            $amountBefore = $creditAmount;
                                            if ($percent_promotion > 0) {
                                                //2024-03-28
                                                $start = Carbon::parse($start_promotion);
                                                $end = Carbon::parse($end_promotion);
                                                $now = Carbon::now();
                                                if ($now->between($start, $end)) {
                                                    $promotion = $creditAmount * $percent_promotion / 100;
                                                    $creditAmount = $creditAmount + $promotion;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                }
                                            }

                                            $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                            if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                $creditAmount = $creditAmount + $promotion_amount;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ Techcombank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                            }

                                            /* telegra */
                                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                $bot_notify = new TelegramSdk();
                                                $bot_notify->botNotify()->sendMessage([
                                                    'chat_id' => siteValue('telegram_chat_id'),
                                                    'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                        '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                        '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                        '🏦 <b>Loại Bank:</b> ' . "Techcombank" . PHP_EOL .
                                                        '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                        '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                        '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                        '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                        '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                    'parse_mode' => 'HTML',
                                                ]);
                                            }

                                            Transaction::create([
                                                'user_id' => $user->id,
                                                'tran_code' => $refNo,
                                                'type' => 'recharge',
                                                'action' => 'add',
                                                'first_balance' => $creditAmount,
                                                'before_balance' => $balance,
                                                'after_balance' => $balance + $creditAmount,
                                                'note' => $note,
                                                'ip' => $request->ip(),
                                                'domain' => $user->domain
                                            ]);

                                            $bill->bank_code = $refNo;
                                            $bill->real_amount = $creditAmount;
                                            $bill->status = 'Success';
                                            $bill->note = $note;
                                            $bill->paid_at = now();
                                            $bill->save();

                                            $user->balance = $balance + $creditAmount;
                                            $user->total_recharge = $total_recharge + $creditAmount;
                                            $user->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($code === 'Acb') {
            $acb = Banking::where('domain', $request->getHost())->where('bank_name', 'ACB')->first();

            if ($acb) {
                $api_token = $acb->token;
                $code_tranfer = strtolower(siteValue('transfer_code'));

                $ch = curl_init('https://api.vpnfast.vn/api/historyacbv2/' . $api_token);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                print_r($response);
                $result = json_decode($response, true);

                $count = 0;
                if (isset($result['transactions'])) {
                    foreach ($result['transactions'] as $key => $item) {
                        $refNo = $item['transactionID'];
                        $description = strtolower($item['description']);
                        $creditAmount = str_replace(",", "", $item['amount']);
                        // $description1 = str_replace(" ", "", $description);
                        $description1 = $description;
                        if ($creditAmount >= $acb->min_recharge && $item['type'] == 'IN') {
                            $checkId = strpos($description1, $code_tranfer);
                            if ($checkId !== false) {
                                $ch1 = explode($code_tranfer, $description1);
                                $ch1 = $ch1[1];
                                $ch1 = str_replace("\n", "", $ch1);
                                $ch2 = explode('.', $ch1);
                                $ch1 = $ch2[0];
                                $ch2 = explode(' ', $ch1);
                                $idUser = $ch2[0];

                                $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                if ($user) {

                                    $refNo = base64_encode($refNo);
                                    $checkTransaction = Recharge::where('bank_code', $refNo)->first();
                                    if ($checkTransaction) {
                                        continue;
                                    } else {
                                        $balance = $user->balance;
                                        $total_recharge = $user->total_recharge;

                                        $percent_promotion = siteValue('percent_promotion');
                                        $start_promotion = siteValue('start_promotion');
                                        $end_promotion = siteValue('end_promotion');

                                        $promotion = 0;

                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                        $amountBefore = $creditAmount;
                                        if ($percent_promotion > 0) {
                                            //2024-03-28
                                            $start = Carbon::parse($start_promotion);
                                            $end = Carbon::parse($end_promotion);
                                            $now = Carbon::now();
                                            if ($now->between($start, $end)) {
                                                $promotion = $creditAmount * $percent_promotion / 100;
                                                $creditAmount = $creditAmount + $promotion;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                            }
                                        }

                                        $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                        if ($promotion && $creditAmount <= $promotion->min_balance) {
                                            $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                            $creditAmount = $creditAmount + $promotion_amount;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                        }

                                        /* telegra */
                                        if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                            $bot_notify = new TelegramSdk();
                                            $bot_notify->botNotify()->sendMessage([
                                                'chat_id' => siteValue('telegram_chat_id'),
                                                'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                    '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                    '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                    '🏦 <b>Loại Bank:</b> ' . "ACB" . PHP_EOL .
                                                    '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                    '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                    '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                    '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                    '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                'parse_mode' => 'HTML',
                                            ]);
                                        }

                                        Transaction::create([
                                            'user_id' => $idUser,
                                            'tran_code' => $refNo,
                                            'type' => 'recharge',
                                            'action' => 'add',
                                            'first_balance' => $creditAmount,
                                            'before_balance' => $balance,
                                            'after_balance' => $balance + $creditAmount,
                                            'note' => $note,
                                            'ip' => $request->ip(),
                                            'domain' => $user->domain
                                        ]);

                                        Recharge::create([
                                            'user_id' => $idUser,
                                            'order_code' => $refNo,
                                            'bank_code' => $refNo,
                                            'payment_method' => 'ACB',
                                            'bank_name' => 'ACB',
                                            'amount' => $amountBefore,
                                            'real_amount' => $creditAmount,
                                            'status' => 'Success',
                                            'note' => $note,
                                            'domain' => $user->domain
                                        ]);

                                        $user->balance = $balance + $creditAmount;
                                        $user->total_recharge = $total_recharge + $creditAmount;
                                        $user->save();
                                    }
                                } else {
                                    $checkTransaction = Recharge::where('order_code', $idUser)->where('type', 'bill')->where('status', '=', 'Pending')->first();
                                    if ($checkTransaction) {
                                        $user = User::find($checkTransaction->user_id);
                                        if (!$user) {
                                            continue;
                                        }
                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            if ($checkTransaction2) {
                                                continue;
                                            } else {
                                                $balance = $user->balance;
                                                $total_recharge = $user->total_recharge;

                                                $percent_promotion = siteValue('percent_promotion');
                                                $start_promotion = siteValue('start_promotion');
                                                $end_promotion = siteValue('end_promotion');

                                                $promotion = 0;

                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                                $amountBefore = $creditAmount;
                                                if ($percent_promotion > 0) {
                                                    //2024-03-28
                                                    $start = Carbon::parse($start_promotion);
                                                    $end = Carbon::parse($end_promotion);
                                                    $now = Carbon::now();
                                                    if ($now->between($start, $end)) {
                                                        $promotion = $creditAmount * $percent_promotion / 100;
                                                        $creditAmount = $creditAmount + $promotion;
                                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                    }
                                                }

                                                $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                                if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                    $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                    $creditAmount = $creditAmount + $promotion_amount;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                                }

                                                /* telegra */
                                                if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                    $bot_notify = new TelegramSdk();
                                                    $bot_notify->botNotify()->sendMessage([
                                                        'chat_id' => siteValue('telegram_chat_id'),
                                                        'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                            '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                            '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                            '🏦 <b>Loại Bank:</b> ' . "ACB" . PHP_EOL .
                                                            '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                            '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                            '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                            '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                            '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                        'parse_mode' => 'HTML',
                                                    ]);
                                                }

                                                Transaction::create([
                                                    'user_id' => $user->id,
                                                    'tran_code' => $refNo,
                                                    'type' => 'recharge',
                                                    'action' => 'add',
                                                    'first_balance' => $creditAmount,
                                                    'before_balance' => $balance,
                                                    'after_balance' => $balance + $creditAmount,
                                                    'note' => $note,
                                                    'ip' => $request->ip(),
                                                    'domain' => $user->domain
                                                ]);

                                                $checkTransaction->bank_code = $refNo;
                                                $checkTransaction->real_amount = $creditAmount;
                                                $checkTransaction->status = 'Success';
                                                $checkTransaction->note = $note;
                                                $checkTransaction->paid_at = now();
                                                $checkTransaction->save();
                                                $user->balance = $balance + $creditAmount;
                                                $user->total_recharge = $total_recharge + $creditAmount;
                                                $user->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $bills = Recharge::where('type', 'bill')->where('status', 'Pending')->where('expired_at', '>', now())->limit(100)->get();

                                foreach ($bills as $bill) {
                                    $order_code = $bill->order_code;

                                    $checkBill = strpos($description, $order_code);
                                    if ($checkBill !== false) {
                                        $idUser = $bill->user_id;
                                        $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                        if (!$user) {
                                            continue;
                                        }

                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            $balance = $user->balance;
                                            $total_recharge = $user->total_recharge;

                                            $percent_promotion = siteValue('percent_promotion');
                                            $start_promotion = siteValue('start_promotion');
                                            $end_promotion = siteValue('end_promotion');

                                            $promotion = 0;

                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                            $amountBefore = $creditAmount;
                                            if ($percent_promotion > 0) {
                                                //2024-03-28
                                                $start = Carbon::parse($start_promotion);
                                                $end = Carbon::parse($end_promotion);
                                                $now = Carbon::now();
                                                if ($now->between($start, $end)) {
                                                    $promotion = $creditAmount * $percent_promotion / 100;
                                                    $creditAmount = $creditAmount + $promotion;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                }
                                            }

                                            $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                            if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                $creditAmount = $creditAmount + $promotion_amount;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ACB. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                            }

                                            /* telegra */
                                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                $bot_notify = new TelegramSdk();
                                                $bot_notify->botNotify()->sendMessage([
                                                    'chat_id' => siteValue('telegram_chat_id'),
                                                    'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                        '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                        '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                        '🏦 <b>Loại Bank:</b> ' . "ACB" . PHP_EOL .
                                                        '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                        '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                        '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                        '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                        '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                    'parse_mode' => 'HTML',
                                                ]);
                                            }

                                            Transaction::create([
                                                'user_id' => $user->id,
                                                'tran_code' => $refNo,
                                                'type' => 'recharge',
                                                'action' => 'add',
                                                'first_balance' => $creditAmount,
                                                'before_balance' => $balance,
                                                'after_balance' => $balance + $creditAmount,
                                                'note' => $note,
                                                'ip' => $request->ip(),
                                                'domain' => $user->domain
                                            ]);

                                            $bill->bank_code = $refNo;
                                            $bill->real_amount = $creditAmount;
                                            $bill->status = 'Success';
                                            $bill->note = $note;
                                            $bill->paid_at = now();
                                            $bill->save();

                                            $user->balance = $balance + $creditAmount;
                                            $user->total_recharge = $total_recharge + $creditAmount;
                                            $user->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($code == 'Viettinbank') {
            $viettinbank = Banking::where('domain', $request->getHost())->where('bank_name', 'Viettinbank')->first();

            if ($viettinbank) {
                $api_token = $viettinbank->token;
                $code_tranfer = strtolower(siteValue('transfer_code'));
                $ch = curl_init('https://api.sieuthicode.net/historyapiviettin/' . $api_token);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                // print_r($response);
                $result = json_decode($response, true);

                $count = 0;
                if (isset($result['transactions'])) {
                    foreach ($result['transactions'] as $key => $item) {
                        $refNo = $item['trxId'];
                        $description = strtolower($item['remark']);
                        $creditAmount = str_replace(",", "", $item['amount']);
                        $creditAmount = str_replace('.00', '', $creditAmount);
                        $description1 = $description;
                        if ($creditAmount >= $viettinbank->min_recharge) {
                            $checkId = strpos($description1, $code_tranfer);
                            if ($checkId !== false) {
                                $ch1 = explode($code_tranfer, $description1);
                                $ch1 = $ch1[1];
                                $ch1 = str_replace("\n", "", $ch1);
                                $ch2 = explode('.', $ch1);
                                $ch1 = $ch2[0];
                                $ch2 = explode(' ', $ch1);
                                $idUser = $ch2[0];

                                $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                if ($user) {

                                    $refNo = base64_encode($refNo);
                                    $checkTransaction = Recharge::where('bank_code', $refNo)->first();
                                    if ($checkTransaction) {
                                        continue;
                                    } else {
                                        $balance = $user->balance;
                                        $total_recharge = $user->total_recharge;

                                        $percent_promotion = siteValue('percent_promotion');
                                        $start_promotion = siteValue('start_promotion');
                                        $end_promotion = siteValue('end_promotion');

                                        $promotion = 0;

                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                        $amountBefore = $creditAmount;
                                        if ($percent_promotion > 0) {
                                            //2024-03-28
                                            $start = Carbon::parse($start_promotion);
                                            $end = Carbon::parse($end_promotion);
                                            $now = Carbon::now();
                                            if ($now->between($start, $end)) {
                                                $promotion = $creditAmount * $percent_promotion / 100;
                                                $creditAmount = $creditAmount + $promotion;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                            }
                                        }

                                        $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                        if ($promotion && $creditAmount <= $promotion->min_balance) {
                                            $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                            $creditAmount = $creditAmount + $promotion_amount;
                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                        }

                                        /* telegra */
                                        if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                            $bot_notify = new TelegramSdk();
                                            $bot_notify->botNotify()->sendMessage([
                                                'chat_id' => siteValue('telegram_chat_id'),
                                                'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                    '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                    '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                    '🏦 <b>Loại Bank:</b> ' . "ViettinBank" . PHP_EOL .
                                                    '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                    '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                    '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                    '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                    '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                'parse_mode' => 'HTML',
                                            ]);
                                        }

                                        Transaction::create([
                                            'user_id' => $idUser,
                                            'tran_code' => $refNo,
                                            'type' => 'recharge',
                                            'action' => 'add',
                                            'first_balance' => $creditAmount,
                                            'before_balance' => $balance,
                                            'after_balance' => $balance + $creditAmount,
                                            'note' => $note,
                                            'ip' => $request->ip(),
                                            'domain' => $user->domain
                                        ]);

                                        Recharge::create([
                                            'user_id' => $idUser,
                                            'order_code' => $refNo,
                                            'bank_code' => $refNo,
                                            'payment_method' => 'Viettinbank',
                                            'bank_name' => 'Viettinbank',
                                            'amount' => $amountBefore,
                                            'real_amount' => $creditAmount,
                                            'status' => 'Success',
                                            'note' => $note,
                                            'domain' => $user->domain
                                        ]);

                                        $user->balance = $balance + $creditAmount;
                                        $user->total_recharge = $total_recharge + $creditAmount;
                                        $user->save();
                                    }
                                } else {
                                    $checkTransaction = Recharge::where('order_code', $idUser)->where('type', 'bill')->where('status', '=', 'Pending')->first();
                                    if ($checkTransaction) {
                                        $user = User::find($checkTransaction->user_id);
                                        if (!$user) {
                                            continue;
                                        }
                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            if ($checkTransaction2) {
                                                continue;
                                            } else {
                                                $balance = $user->balance;
                                                $total_recharge = $user->total_recharge;

                                                $percent_promotion = siteValue('percent_promotion');
                                                $start_promotion = siteValue('start_promotion');
                                                $end_promotion = siteValue('end_promotion');

                                                $promotion = 0;

                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                                $amountBefore = $creditAmount;
                                                if ($percent_promotion > 0) {
                                                    //2024-03-28
                                                    $start = Carbon::parse($start_promotion);
                                                    $end = Carbon::parse($end_promotion);
                                                    $now = Carbon::now();
                                                    if ($now->between($start, $end)) {
                                                        $promotion = $creditAmount * $percent_promotion / 100;
                                                        $creditAmount = $creditAmount + $promotion;
                                                        $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                    }
                                                }

                                                $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                                if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                    $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                    $creditAmount = $creditAmount + $promotion_amount;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                                }

                                                /* telegra */
                                                if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                    $bot_notify = new TelegramSdk();
                                                    $bot_notify->botNotify()->sendMessage([
                                                        'chat_id' => siteValue('telegram_chat_id'),
                                                        'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                            '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                            '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                            '🏦 <b>Loại Bank:</b> ' . "ViettinBank" . PHP_EOL .
                                                            '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                            '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                            '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                            '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                            '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                        'parse_mode' => 'HTML',
                                                    ]);
                                                }

                                                Transaction::create([
                                                    'user_id' => $user->id,
                                                    'tran_code' => $refNo,
                                                    'type' => 'recharge',
                                                    'action' => 'add',
                                                    'first_balance' => $creditAmount,
                                                    'before_balance' => $balance,
                                                    'after_balance' => $balance + $creditAmount,
                                                    'note' => $note,
                                                    'ip' => $request->ip(),
                                                    'domain' => $user->domain
                                                ]);

                                                $checkTransaction->bank_code = $refNo;
                                                $checkTransaction->real_amount = $creditAmount;
                                                $checkTransaction->status = 'Success';
                                                $checkTransaction->note = $note;
                                                $checkTransaction->paid_at = now();
                                                $checkTransaction->save();
                                                $user->balance = $balance + $creditAmount;
                                                $user->total_recharge = $total_recharge + $creditAmount;
                                                $user->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $bills = Recharge::where('type', 'bill')->where('status', 'Pending')->where('expired_at', '>', now())->limit(100)->get();

                                foreach ($bills as $bill) {
                                    $order_code = $bill->order_code;

                                    $checkBill = strpos($description, $order_code);
                                    if ($checkBill !== false) {
                                        $idUser = $bill->user_id;
                                        $user = User::where('domain', request()->getHost())->where('id', $idUser)->first();
                                        if (!$user) {
                                            continue;
                                        }

                                        $refNo = base64_encode($refNo);
                                        $checkTransaction2 = Recharge::where('bank_code', $refNo)->first();
                                        if ($checkTransaction2) {
                                            continue;
                                        } else {
                                            $balance = $user->balance;
                                            $total_recharge = $user->total_recharge;

                                            $percent_promotion = siteValue('percent_promotion');
                                            $start_promotion = siteValue('start_promotion');
                                            $end_promotion = siteValue('end_promotion');

                                            $promotion = 0;

                                            $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ";
                                            $amountBefore = $creditAmount;
                                            if ($percent_promotion > 0) {
                                                //2024-03-28
                                                $start = Carbon::parse($start_promotion);
                                                $end = Carbon::parse($end_promotion);
                                                $now = Carbon::now();
                                                if ($now->between($start, $end)) {
                                                    $promotion = $creditAmount * $percent_promotion / 100;
                                                    $creditAmount = $creditAmount + $promotion;
                                                    $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion) . " VNĐ";
                                                }
                                            }

                                            $promotion = RechargePromotion::where('min_balance', '<=', $creditAmount)->where('status', 'active')->where('domain', request()->getHost())->first();
                                            if ($promotion && $creditAmount <= $promotion->min_balance) {
                                                $promotion_amount = $creditAmount * $promotion->percentage / 100;
                                                $creditAmount = $creditAmount + $promotion_amount;
                                                $note = "Bạn đã nạp thành công " . number_format($creditAmount) . " VNĐ từ ViettinBank. Số dư tài khoản của bạn là " . number_format($balance + $creditAmount) . " VNĐ. Bạn được khuyến mãi " . number_format($promotion_amount) . " VNĐ";
                                            }

                                            /* telegram */
                                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                                $bot_notify = new TelegramSdk();
                                                $bot_notify->botNotify()->sendMessage([
                                                    'chat_id' => siteValue('telegram_chat_id'),
                                                    'text' => '🎉 <b>Thông báo nạp tiền</b> 🎉' . PHP_EOL .
                                                        '👤 <b>Người nạp:</b> ' . $user->username . PHP_EOL .
                                                        '💰 <b>Số tiền:</b> ' . number_format($creditAmount) . ' VNĐ' . PHP_EOL .
                                                        '🏦 <b>Loại Bank:</b> ' . "ViettinBank" . PHP_EOL .
                                                        '📝 <b>Ghi chú:</b> ' . $note . PHP_EOL .
                                                        '🔗 <b>Mã giao dịch:</b> ' . base64_decode($refNo) . PHP_EOL .
                                                        '📅 <b>Thời gian:</b> ' . Carbon::now()->format('d/m/Y H:i:s') . PHP_EOL .
                                                        '🔗 <b>IP:</b> ' . $request->ip() . PHP_EOL .
                                                        '🌐 <b>Domain:</b> ' . $request->getHost(),
                                                    'parse_mode' => 'HTML',
                                                ]);
                                            }

                                            Transaction::create([
                                                'user_id' => $user->id,
                                                'tran_code' => $refNo,
                                                'type' => 'recharge',
                                                'action' => 'add',
                                                'first_balance' => $creditAmount,
                                                'before_balance' => $balance,
                                                'after_balance' => $balance + $creditAmount,
                                                'note' => $note,
                                                'ip' => $request->ip(),
                                                'domain' => $user->domain
                                            ]);

                                            $bill->bank_code = $refNo;
                                            $bill->real_amount = $creditAmount;
                                            $bill->status = 'Success';
                                            $bill->note = $note;
                                            $bill->paid_at = now();
                                            $bill->save();

                                            $user->balance = $balance + $creditAmount;
                                            $user->total_recharge = $total_recharge + $creditAmount;
                                            $user->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function checkBillExpired()
    {
        $bills = Recharge::where('status', 'Pending')->where('type', 'bill')->where('expired_at', '<', now())->get();
        foreach ($bills as $bill) {
            $bill->status = 'Failed';
            $bill->save();
        }
    }
}
