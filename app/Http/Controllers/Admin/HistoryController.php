<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\Order;
use App\Models\Recharge;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HistoryController extends Controller
{

    public function viewHistoryUser(Request $request)
    {

        $search = $request->get('search');
        $transactions = Transaction::where('domain', $request->getHost())
            ->when($search, function ($query) use ($search) {
                return $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%');
                    })
                    ->orWhere('tran_code', 'like', '%' . $search . '%')
                    ->orWhere('first_balance', 'like', '%' . $search . '%')
                    ->orWhere('before_balance', 'like', '%' . $search . '%')
                    ->orWhere('after_balance', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')->paginate(1);

        return view('admin.history.users', compact('transactions'));
    }

    public function viewHistoryOrders(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $currentDomain = $request->getHost();

        $orders = Order::where('domain', $currentDomain)
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('username', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('server', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('service', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhere('order_code', 'like', '%' . $search . '%')
                        ->orWhere('object_id', 'like', '%' . $search . '%');
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'desc')->paginate(10);

        return view('admin.history.orders', compact('orders'));
    }

    public function viewHistoryPayment(Request $request)
    {
        $search = $request->get('search');

        $payments = Recharge::where('domain', $request->getHost())
            ->when($search, function ($query) use ($search) {
                return $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%');
                    })
                    ->orWhere('order_code', 'like', '%' . $search . '%')
                    ->orWhere('payment_method', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')->paginate(10);

        return view('admin.history.payment', compact('payments'));
    }

    public function viewHistoryTransactions(Request $request)
    {
        $search = $request->get('search');

        $transactions = Transaction::where('domain', $request->getHost())
            ->when($search, function ($query) use ($search) {
                return $query->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%');
                    })
                    ->orWhere('tran_code', 'like', '%' . $search . '%')
                    ->orWhere('first_balance', 'like', '%' . $search . '%')
                    ->orWhere('before_balance', 'like', '%' . $search . '%')
                    ->orWhere('after_balance', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')->paginate(10);

        return view('admin.history.transactions', compact('transactions'));
    }

    public function orderAction(Request $request, $id)
    {
        $order = Order::where('domain', request()->getHost())->find($id);
        if ($order) {

            $valid = Validator::make($request->all(), [
                'status' => 'required',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', 'Vui lòng chọn trạng thái');
            } else {
                if ($request->status == 'Refunded') {
                    $orderData = json_decode($order->order_data);
                    $quantity = $orderData->quantity;
                    $price = $orderData->price;

                    if ($quantity > $order->start) {
                        $returned = $quantity - $order->buff;
                    } else {
                        $returned = $quantity;
                    }
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
                }
                $order->status = $request->status;
                $order->start = $request->start ?? $order->start;
                $order->buff = $request->buff ?? $order->buff;

                $order->save();
                return redirect()->back()->with('success', 'Cập nhật đơn hàng thành công');
            }
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }
    }

    public function deleteOrder($id)
    {
        $order = Order::where('domain', request()->getHost())->find($id);
        if ($order) {
            $order->delete();
            return redirect()->back()->with('success', 'Xóa đơn hàng thành công');
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }
    }

    public function viewEditOrder($id)
    {
        $order = Order::where('domain', request()->getHost())->find($id);
        if ($order) {
            return view('admin.history.edit-order', compact('order'));
        } else {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }
    }
}
