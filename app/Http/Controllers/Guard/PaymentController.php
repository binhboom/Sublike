<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\Banking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Recharge;

class PaymentController extends Controller
{
    public function createBill(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'bank_code' => 'required',
            'amount' => 'required|min:10000|numeric',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $code = mt_rand(100000, 999999);
            $bill = new Recharge();
            $bill->user_id = auth()->user()->id;
            $bill->order_code = $code;
            $bill->type = 'bill';
            $bill->payment_method = base64_decode($request->bank_code);
            $bill->bank_name = base64_decode($request->bank_code);
            $bill->bank_code = null;
            $bill->amount = $request->amount;
            $bill->real_amount = 0;
            $bill->status = 'Pending';
            $bill->note = null;
            $bill->domain = $request->getHost();
            $bill->expired_at = now()->addMinutes(30);
            $bill->save();

            return redirect()->route('payment.bill', $code);
        }
    }

    public function viewBill($code)
    {
        $bill = Recharge::where('order_code', $code)->where('user_id', auth()->user()->id)->where('domain', request()->getHost())->first();
        if ($bill) {

            if ($bill->status == 'Success') {
                return redirect()->route('home');
            }

            if ($bill->status == 'Failed') {
                return redirect()->route('home');
            }

            if ($bill->expired_at < now()) {
                $bill->status = 'Failed';
                $bill->save();
                return redirect()->route('home');
            }

            $banking = Banking::where('bank_name', $bill->bank_name)->where('domain', request()->getHost())->first();
            if (!$banking) {
                return redirect()->route('home');
            }

            return view('guard.payment.bill', compact('bill', 'banking'));
        } else {
            return redirect()->route('home');
        }
    }

    public function checkPayment($code)
    {
        $bill = Recharge::where('order_code', $code)->where('user_id', auth()->user()->id)->where('domain', request()->getHost())->first();
        if ($bill) {

            if ($bill->expired_at < now()) {
                $bill->status = 'Failed';
                $bill->save();
                return response()->json(['status' => 'error', 'message' => 'Thanh toán thất bại hoặc hết hạn']);
            }
            if ($bill->status == 'Success' && $bill->is_read !== 1) {
                $bill->is_read = 1;
                $bill->save();
                return response()->json(['status' => 'success', 'message' => 'Thanh toán thành công']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Thanh toán chưa được xác nhận']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng']);
        }
    }
}
