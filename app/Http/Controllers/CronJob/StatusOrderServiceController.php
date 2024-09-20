<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Api\Service\AutolikezController;
use App\Http\Controllers\Api\Service\BaostarController;
use App\Http\Controllers\Api\Service\BoosterviewsController;
use App\Http\Controllers\Api\Service\CheoTuongTacController;
use App\Http\Controllers\Api\Service\Hacklike17Controller;
use App\Http\Controllers\Api\Service\SmmcoderController;
use App\Http\Controllers\Api\Service\SmmCustomController;
use App\Http\Controllers\Api\Service\SmmgenController;
use App\Http\Controllers\Api\Service\SmmKingController;
use App\Http\Controllers\Api\Service\SmmFollowsController;
use App\Http\Controllers\Api\Service\SubgiareController;
use App\Http\Controllers\Api\Service\TraodoisubController;
use App\Http\Controllers\Api\Service\TrumsubreController;
use App\Http\Controllers\Api\Service\TuongTacSaleController;
use App\Http\Controllers\Api\Service\TwoMxhController;
use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\Order;
use App\Models\ServiceServer;
use App\Models\SmmPanelPartner;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Switch_;

class StatusOrderServiceController extends Controller
{

    public function cronJobStatusServiceSmmking(Request $request)
    {
        $orders = Order::where('orderProviderName', 'smmking')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $smmking = new SmmKingController();
        $result = $smmking->multiStatus($listOrder);

        if (isset($result) && $result['success'] == true) {
            print_r($result);
            foreach ($result as $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $data['id'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);

                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }

                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceBoosterviews(Request $request)
    {
        $orders = Order::where('orderProviderName', 'boosterviews')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $boosterviews = new BoosterviewsController();
        $result = $boosterviews->multiStatus($listOrder);

        if (isset($result) && $result['success'] == true) {
            foreach ($result as $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $data['id'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceBaostar(Request $request)
    {
        $orders = Order::where('orderProviderName', 'baostar')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();


        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $baostar = new BaostarController();
        $result = $baostar->order($listOrder);

        if (isset($data['success']) && $data['success']) {
            foreach ($result['data'] as $data) {
                $id = $data['id'];
                $start_like = $data['start_like'] ?? 0;
                $count_is_run = $data['count_is_run'] ?? 0;
                $status = $data['status'] ?? 'Active';

                $order = Order::where('order_id', $id)->first();

                if ($order) {
                    switch ($status) {
                        case 'Active':
                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Running';
                            break;
                        case 'done':
                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Completed';
                            break;
                        case 'processing':
                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Running';
                            break;
                        case '4':
                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Cancelled';
                            break;
                        case '3':
                            // refund
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $count_is_run) {
                                $returned = $quantity - $count_is_run;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_like;
                            $order->buff = $count_is_run;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusService2mxh(Request $request)
    {
        $orders = Order::where('orderProviderName', '2mxh')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();
        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');
        $twoMxh = new TwoMxhController();
        $result = $twoMxh->order($listOrder);

        if (isset($result) && $result['status'] == 'success') {
            // print_r($result);
            foreach ($result['data'] as $data) {
                $start_number = $data['start_number'];
                $success_count = $data['success_count'];
                $status = $data['status'];
                $id = $data['id'];
                $order = Order::where('order_id', $id)->first();
                if ($order) {
                    switch ($status) {
                        case 'Running':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Cancelled';
                            break;
                        case 'Failed':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Failed';
                            break;
                        case 'Paused':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Cancelled';
                            break;
                        case 'Error':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Failed';
                            break;
                        case 'WaitingForRefund':
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Cancelled';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $success_count) {
                                $returned = $quantity - $success_count;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned * $price,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_number;
                            $order->buff = $success_count;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceSubgiare(Request $request)
    {
        $orders = Order::where('orderProviderName', 'subgiare')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();
        $listOrder = [];

        foreach ($orders as $order) {
            $checkServer = $order->server;
            if ($checkServer) {
                if (in_array($checkServer->providerLink, $listOrder)) {
                    $listOrder['code'] = $listOrder['code'] . ',' . $order->order_id;
                } else {
                    $listOrder['path'] = $checkServer->providerLink;
                    $listOrder['code'] = $order->order_id;
                }
            }
        }

        if (count($listOrder) > 0) {
            $subgiare = new SubgiareController();
            foreach ($listOrder as $order) {
                $subgiare->path = $order['path'];
                $result = $subgiare->order($order['code']);

                if (isset($result)) {
                    foreach ($result['data'] as $data) {
                        $start = $data['start'];
                        $buff = $data['buff'];
                        $status = $data['status'];
                        $code_order = $data['code_order'];

                        $order = Order::where('order_id', $code_order)->first();
                        if ($order) {
                            switch ($status) {
                                case 'Active':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Running';
                                    break;
                                case 'Success':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Completed';
                                    break;
                                case 'Report':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Failed';
                                    break;
                                case 'Pause':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Error':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Failed';
                                    break;
                                case 'Refund':
                                    $orderData = json_decode($order->order_data);
                                    $quantity = $orderData->quantity;
                                    $price = $orderData->price;

                                    if ($quantity > $buff) {
                                        $returned = $quantity - $buff;
                                    } else {
                                        $returned = $quantity;
                                    }

                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Refunded';

                                    $tranCode = 'INV_' . rand(1000000, 9999999);
                                    Transaction::create([
                                        'user_id' => $order->user_id,
                                        'tran_code' => $tranCode,
                                        'type' => 'refund',
                                        'action' => 'add',
                                        'first_balance' => $returned,
                                        'before_balance' => $order->user->balance,
                                        'after_balance' => $order->user->balance + ceil($returned * $price),
                                        'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                        'ip' => $request->ip(),
                                        'domain' => $order->domain,
                                    ]);

                                    $order->user->balance += ceil($returned * $price);
                                    $order->user->save();

                                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                        $bot_notify = new TelegramSdk();
                                        $bot_notify->botNotify()->sendMessage([
                                            'chat_id' => siteValue('telegram_chat_id'),
                                            'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                            'parse_mode' => 'HTML',
                                        ]);
                                    }
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        }
                    }
                }
            }
        }
    }

    public function cronJobStatusServiceTrumsubre(Request $request)
    {
        $orders = Order::where('orderProviderName', 'trumsubre')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();
        $listOrder = [];

        foreach ($orders as $order) {
            $checkServer = $order->server;
            if ($checkServer) {
                if (in_array($checkServer->providerLink, $listOrder)) {
                    // $listOrder['code'] = $listOrder['code'] . ',' . $order->order_id;
                    $listOrder[] = [
                        'code' => $order->order_id
                    ];
                } else {
                    // $listOrder['path'] = $checkServer->providerLink;
                    // $listOrder['code'] = $order->order_id;
                    $listOrder[] = [
                        'path' => $checkServer->providerLink,
                        'code' => $order->order_id
                    ];
                }
            }
        }

        if (count($listOrder) > 0) {
            $subgiare = new TrumsubreController();
            foreach ($listOrder as $order) {
                $subgiare->path = $order['path'];
                $result = $subgiare->order($order['code']);

                if (isset($result)) {
                    foreach ($result['data'] as $data) {
                        $start = $data['start'];
                        $buff = $data['buff'];
                        $status = $data['status'];
                        $code_order = $data['code_order'];

                        $order = Order::where('order_id', $code_order)->first();
                        if ($order) {
                            switch ($status) {
                                case 'Active':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Running';
                                    break;
                                case 'Success':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Completed';
                                    break;
                                case 'Report':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Failed';
                                    break;
                                case 'Pause':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Error':
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Failed';
                                    break;
                                case 'Refund':
                                    $orderData = json_decode($order->order_data);
                                    $quantity = $orderData->quantity;
                                    $price = $orderData->price;

                                    if ($quantity > $buff) {
                                        $returned = $quantity - $buff;
                                    } else {
                                        $returned = $quantity;
                                    }

                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Refunded';

                                    $tranCode = 'INV_' . rand(1000000, 9999999);
                                    Transaction::create([
                                        'user_id' => $order->user_id,
                                        'tran_code' => $tranCode,
                                        'type' => 'refund',
                                        'action' => 'add',
                                        'first_balance' => $returned,
                                        'before_balance' => $order->user->balance,
                                        'after_balance' => $order->user->balance + ceil($returned * $price),
                                        'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                        'ip' => $request->ip(),
                                        'domain' => $order->domain,
                                    ]);

                                    $order->user->balance += ceil($returned * $price);
                                    $order->user->save();

                                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                        $bot_notify = new TelegramSdk();
                                        $bot_notify->botNotify()->sendMessage([
                                            'chat_id' => siteValue('telegram_chat_id'),
                                            'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                            'parse_mode' => 'HTML',
                                        ]);
                                    }
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $buff;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        }
                    }
                }
            }
        }
    }

    public function cronJobStatusServiceHacklike17(Request $request)
    {
        $orders = Order::where('orderProviderName', 'hacklike17')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $orderId = $order->order_id;
            $hacklike17 = new Hacklike17Controller();

            $result = $hacklike17->statusOrder($orderId);
            // print_r($result);
            if (isset($result) && $result['status'] === 1) {
                $present = $result['data'][0]['present'];
                $original = $result['data'][0]['original'];
                $status = $result['data'][0]['status'];

                $order = Order::where('order_id', $orderId)->first();

                if ($order) {

                    switch ($status) {
                        case '-1':
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Processing';
                            break;
                        case '0':
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Running';
                            break;
                        case '1':
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Completed';
                            break;
                        case '2':
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Failed';
                            break;
                        case '2':
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Cancelled';
                            break;
                            // case '3':
                            //     $order->start = $present;
                            //     $order->buff = $original;
                            //     $order->status = 'Failed';
                            //     break;
                        case '3':

                            $start = $present;
                            $buff = $original;
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $buff) {
                                $returned = $quantity - $buff;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $present;
                            $order->buff = $original;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceCheotuongtac(Request $request)
    {
        $orders = Order::where('orderProviderName', 'cheotuongtac')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            // thêmm mã đơn hàng vào list
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');
        // echo $listOrder;
        $cheotuongtac = new CheoTuongTacController();
        $result = $cheotuongtac->multiStatus($listOrder);

        if (isset($result)) {
            // print_r($result);
            // die();
            foreach ($result as $key => $data) {
                if (isset($data['error'])) {
                    continue;
                }
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $key;

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        case 'Partial':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        case 'Partial':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceTuongtacsale(Request $request)
    {
        $orders = Order::where('orderProviderName', 'tuongtacsale')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $tuongtacsale = new TuongTacSaleController();
        $result = $tuongtacsale->multiStatus($listOrder);

        if (isset($result)) {
            foreach ($result as $key => $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $key;

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceSmmgen(Request $request)
    {
        $orders = Order::where('orderProviderName', 'smmgen')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $smm = new SmmgenController();
        $result = $smm->multiStatus($listOrder);

        if (isset($result) && isset($result['success'])) {
            foreach ($result as $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $data['id'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceSmmcoder(Request $request)
    {
        $orders = Order::where('orderProviderName', 'smmcoder')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $smm = new SmmcoderController();
        $result = $smm->multiStatus($listOrder);

        if (isset($result) && !$result['error']) {
            foreach ($result as $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $data['id'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? $orderData->quantity : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceSmmFollows(Request $request)
    {
        $orders = Order::where('orderProviderName', 'smmfollows')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $smmking = new SmmFollowsController();
        $result = $smmking->multiStatus($listOrder);

        if (isset($result) && $result['success'] == true) {
            print_r($result);
            foreach ($result as $data) {
                $start_count = $data['start_count'];
                $status = $data['status'];
                $remains = $data['remains'];
                $id = $data['id'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {

                    $orderData = json_decode($order->order_data);

                    $remains = $remains <= 0 ? 0 : $orderData->quantity - $remains;

                    switch ($status) {
                        case 'In progress':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                        case 'Completed':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Completed';
                            break;
                        case 'Canceled':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Cancelled';
                            break;
                        case 'Preparing':
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Pending';
                            break;
                        case 'Refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $remains) {
                                $returned = $quantity - $remains;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);

                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }

                            break;
                        default:
                            $order->start = $start_count;
                            $order->buff = $remains;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceAutolikez(Request $request)
    {
        $orders = Order::where('orderProviderName', 'autolikez')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        $listOrder = "";

        foreach ($orders as $order) {
            $listOrder .= $order->order_id . ',';
        }

        $listOrder = rtrim($listOrder, ',');

        $autolikez = new AutolikezController();
        $result = $autolikez->getOrder($listOrder);
        if ($result['success'] == true) {
            foreach ($result['data'] as $key => $value) {
                $id = $value['id'];
                $start = $value['start'] ?? 0;
                $buff = $value['count_is_run'] ?? 0;
                $status = $value['status'];

                $order = Order::where('order_id', $id)->first();

                if ($order) {
                    switch ($status) {
                        case 'run':
                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Running';
                            break;
                        case 'done':
                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Completed';
                            break;
                        case 'remove':
                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Cancelled';
                            break;
                        case 'error':
                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Failed';
                            break;
                        case 'refund':
                            $orderData = json_decode($order->order_data);
                            $quantity = $orderData->quantity;
                            $price = $orderData->price;

                            if ($quantity > $buff) {
                                $returned = $quantity - $buff;
                            } else {
                                $returned = $quantity;
                            }

                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Refunded';

                            $tranCode = 'INV_' . rand(1000000, 9999999);
                            Transaction::create([
                                'user_id' => $order->user_id,
                                'tran_code' => $tranCode,
                                'type' => 'refund',
                                'action' => 'add',
                                'first_balance' => $returned,
                                'before_balance' => $order->user->balance,
                                'after_balance' => $order->user->balance + ceil($returned * $price),
                                'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                'ip' => $request->ip(),
                                'domain' => $order->domain,
                            ]);

                            $order->user->balance += ceil($returned * $price);
                            $order->user->save();

                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                    'parse_mode' => 'HTML',
                                ]);
                            }
                            break;
                        default:
                            $order->start = $start;
                            $order->buff = $buff;
                            $order->status = 'Running';
                            break;
                    }
                    $order->save();
                }
            }
        }
    }

    public function cronJobStatusServiceTds(Request $request)
    {
        $orders = Order::where('orderProviderName', 'traodoisub')
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        foreach ($orders as $key => $order) {

            $tds = new TraodoisubController();
            $tds->path = $order->orderProviderServer;

            $result = $tds->status($order->object_id);
            // print_r($result);
            if (isset($result)) {

                if (count($result['data']) <= 0) {
                    $result = $tds->status($order->object_id, true);
                    foreach ($result['data'] as $key => $value) {
                        $sl = $value['sl'];
                        $start = $value['start'] ?? $sl;
                        $datang = $value['datang'];
                        $status = $value['status'];
                        $note = $value['note'];
                        $link = $value['link'] ?? $value['real_id'] ?? $value['idpost'];

                        if ($note == $order->order_code) {
                            switch ($status) {
                                case '<span class="badge badge badge-soft-success">Đang Chạy</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                                case '<span class="badge badge badge-soft-primary">Hoàn Thành</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Completed';
                                    break;
                                case '<span class="badge badge badge-soft-danger">Lỗi</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Failed';
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        } elseif (strpos($link, $order->object_id) !== false) {
                            switch ($status) {
                                case '<span class="badge badge badge-soft-success">Đang Chạy</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                                case '<span class="badge badge badge-soft-primary">Hoàn Thành</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Completed';
                                    break;
                                case '<span class="badge badge badge-soft-danger">Lỗi</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Failed';
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        }
                    }
                } else {
                    foreach ($result['data'] as $key => $value) {
                        $sl = $value['sl'];
                        $start = $value['start'] ?? $sl;
                        $datang = $value['datang'];
                        $status = $value['status'];
                        $note = $value['note'];
                        $link = $value['link'] ?? $value['real_id'] ?? $value['idpost'];

                        if ($note == $order->order_code) {
                            switch ($status) {
                                case '<span class="badge badge badge-soft-success">Đang Chạy</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                                case '<span class="badge badge badge-soft-primary">Hoàn Thành</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Completed';
                                    break;
                                case '<span class="badge badge badge-soft-danger">Lỗi</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Failed';
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        } elseif (strpos($link, $order->object_id) !== false) {
                            switch ($status) {
                                case '<span class="badge badge badge-soft-success">Đang Chạy</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                                case '<span class="badge badge badge-soft-primary">Hoàn Thành</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Completed';
                                    break;
                                case '<span class="badge badge badge-soft-danger">Lỗi</span>':
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Failed';
                                    break;
                                default:
                                    $order->start = $start;
                                    $order->buff = $datang;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        }
                    }
                }
            }
        }
    }

    public function cronJobStatusServiceSmm(Request $request, $name)
    {
        $smmCheck = SmmPanelPartner::where('name', $name)->where('domain', env('APP_MAIN_SITE'))->first();
        if ($smmCheck) {
            $smm = new SmmCustomController();
            $smm->api_url = $smmCheck->url_api;
            $smm->api_key = $smmCheck->api_token;

            $orders = Order::where('orderProviderName', $name)
                ->where('status', '!=', 'Completed')
                ->where('status', '!=', 'Cancelled')
                ->where('status', '!=', 'Refunded')
                ->where('status', '!=', 'Failed')
                ->where('status', '!=', 'Partially Refunded')
                ->where('status', '!=', 'Partially Completed')
                ->orderBy('id', 'desc')->limit(100)->get();

            $listOrder = "";

            foreach ($orders as $order) {
                $listOrder .= $order->order_id . ',';
            }

            $listOrder = rtrim($listOrder, ',');

            $result = $smm->multiStatus($listOrder);

            if (isset($result) && !isset($result['error'])) {

                if (count($result) > 0) {
                    foreach ($result as $key => $data) {
                        $start_count = $data['start_count'];
                        $status = $data['status'];
                        $remains = $data['remains'];
                        $id = $data['id'] ?? $key;

                        $order = Order::where('order_id', $id)->first();

                        if ($order) {

                            $orderData = json_decode($order->order_data);

                            $remains = $remains <= 0 ? $orderData->quantity : $orderData->quantity - $remains;

                            switch ($status) {
                                case 'In progress':
                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Running';
                                    break;
                                case 'Completed':
                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Completed';
                                    break;
                                case 'Canceled':
                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Preparing':
                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Pending';
                                    break;
                                case 'Refund':
                                    $orderData = json_decode($order->order_data);
                                    $quantity = $orderData->quantity;
                                    $price = $orderData->price;

                                    if ($quantity > $remains) {
                                        $returned = $quantity - $remains;
                                    } else {
                                        $returned = $quantity;
                                    }

                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Refunded';

                                    $tranCode = 'INV_' . rand(1000000, 9999999);
                                    Transaction::create([
                                        'user_id' => $order->user_id,
                                        'tran_code' => $tranCode,
                                        'type' => 'refund',
                                        'action' => 'add',
                                        'first_balance' => $returned,
                                        'before_balance' => $order->user->balance,
                                        'after_balance' => $order->user->balance + ceil($returned * $price),
                                        'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                        'ip' => $request->ip(),
                                        'domain' => $order->domain,
                                    ]);

                                    $order->user->balance += ceil($returned * $price);
                                    $order->user->save();

                                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                        $bot_notify = new TelegramSdk();
                                        $bot_notify->botNotify()->sendMessage([
                                            'chat_id' => siteValue('telegram_chat_id'),
                                            'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                            'parse_mode' => 'HTML',
                                        ]);
                                    }
                                    break;
                                default:
                                    $order->start = $start_count;
                                    $order->buff = $remains;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        }
                    }
                } else {
                    $start_count = $result['start_count'];
                    $status = $result['status'];
                    $remains = $result['remains'];
                    $id = $result['id'] ?? $listOrder;

                    $order = Order::where('order_id', $listOrder)->first();

                    if ($order) {

                        $orderData = json_decode($order->order_data);

                        $remains = $remains <= 0 ? $orderData->quantity : $orderData->quantity - $remains;

                        switch ($status) {
                            case 'In progress':
                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Running';
                                break;
                            case 'Completed':
                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Completed';
                                break;
                            case 'Canceled':
                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Cancelled';
                                break;
                            case 'Preparing':
                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Pending';
                                break;
                            case 'Refund':
                                $orderData = json_decode($order->order_data);
                                $quantity = $orderData->quantity;
                                $price = $orderData->price;

                                if ($quantity > $remains) {
                                    $returned = $quantity - $remains;
                                } else {
                                    $returned = $quantity;
                                }

                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Refunded';

                                $tranCode = 'INV_' . rand(1000000, 9999999);
                                Transaction::create([
                                    'user_id' => $order->user_id,
                                    'tran_code' => $tranCode,
                                    'type' => 'refund',
                                    'action' => 'add',
                                    'first_balance' => $returned,
                                    'before_balance' => $order->user->balance,
                                    'after_balance' => $order->user->balance + ceil($returned * $price),
                                    'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                    'ip' => $request->ip(),
                                    'domain' => $order->domain,
                                ]);

                                $order->user->balance += ceil($returned * $price);
                                $order->user->save();

                                if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                    $bot_notify = new TelegramSdk();
                                    $bot_notify->botNotify()->sendMessage([
                                        'chat_id' => siteValue('telegram_chat_id'),
                                        'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                        'parse_mode' => 'HTML',
                                    ]);
                                }
                                break;
                            default:
                                $order->start = $start_count;
                                $order->buff = $remains;
                                $order->status = 'Running';
                                break;
                        }
                        $order->save();
                    }
                }
            }
        }
    }

    public function updateOrders(Request $request)
    {
        $orders = Order::where('domain', $request->getHost())
            ->where('status', '!=', 'Completed')
            ->where('status', '!=', 'Cancelled')
            ->where('status', '!=', 'Refunded')
            ->where('status', '!=', 'Failed')
            ->where('status', '!=', 'Partially Refunded')
            ->where('status', '!=', 'Partially Completed')
            ->orderBy('id', 'desc')->limit(100)->get();

        foreach ($orders as $order) {
            $orderMain = Order::where('id', $order->order_id)->where('domain', site('is_domain'))->first();
            if ($orderMain) {
                $order->status = $orderMain->status;
                $order->save();
            }
        }
    }
}
