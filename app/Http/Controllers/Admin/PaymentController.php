<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banking;
use App\Models\ConfigSite;
use App\Models\RechargePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function viewPaymentConfig()
    {

        $momo = Banking::where('domain', request()->getHost())->where('bank_name', 'Momo')->first();
        $mbbank = Banking::where('domain', request()->getHost())->where('bank_name', 'MBBank')->first();
        $techcombank = Banking::where('domain', request()->getHost())->where('bank_name', 'Techcombank')->first();
        $acb = Banking::where('domain', request()->getHost())->where('bank_name', 'ACB')->first();
        $viettinbank = Banking::where('domain', request()->getHost())->where('bank_name', 'Viettinbank')->first();

        // nếu chưa có thông tin ngân hàng thì tạo mới thông tin ngân hàng
        if (!$momo) {
            $momo = new Banking();
            $momo->bank_name = 'Momo';
            $momo->domain = request()->getHost();
            $momo->status = 'inactive';
            $momo->save();
        }

        if (!$mbbank) {
            $mbbank = new Banking();
            $mbbank->bank_name = 'MBBank';
            $mbbank->domain = request()->getHost();
            $mbbank->status = 'inactive';
            $mbbank->save();
        }

        if (!$techcombank) {
            $techcombank = new Banking();
            $techcombank->bank_name = 'Techcombank';
            $techcombank->domain = request()->getHost();
            $techcombank->status = 'inactive';
            $techcombank->save();
        }
        
        if (!$acb) {
            $acb = new Banking();
            $acb->bank_name = 'ACB';
            $acb->domain = request()->getHost();
            $acb->status = 'inactive';
            $acb->save();
        }

        if (!$viettinbank) {
            $viettinbank = new Banking();
            $viettinbank->bank_name = 'Viettinbank';
            $viettinbank->domain = request()->getHost();
            $viettinbank->status = 'inactive';
            $viettinbank->save();
        }

        $listRechargePromotion = RechargePromotion::where('domain', request()->getHost())->paginate(10);

        return view('admin.payment.config', compact('momo', 'mbbank', 'techcombank', 'acb', 'viettinbank', 'listRechargePromotion'));
    }

    public function updatePaymentConfig(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'start_promotion' => 'required|date',
            'end_promotion' => 'required|date',
            'percent_promotion' => 'required|numeric',
            'transfer_code' => 'required|string',
            'partner_id' => 'required|string',
            'partner_key' => 'required|string',
            'percent_card' => 'required|numeric',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $site = ConfigSite::where('domain', $request->getHost())->first();
            $site->start_promotion = $request->start_promotion;
            $site->end_promotion = $request->end_promotion;
            $site->percent_promotion = $request->percent_promotion;
            $site->transfer_code = $request->transfer_code;
            $site->partner_id = $request->partner_id;
            $site->partner_key = $request->partner_key;
            $site->percent_card = $request->percent_card;
            $site->save();

            return redirect()->back()->with('success', 'Cập nhật cấu hình thanh toán thành công');
        }
    }

    public function updatePayment(Request $request, $bank_name)
    {
        if ($bank_name === 'Momo') {
            $valid = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'min_recharge' => 'required|numeric',
                'api_key' => 'required|string',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $bank = Banking::where('domain', request()->getHost())->where('bank_name', $bank_name)->first();
                $bank->status = $request->status;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->min_recharge = $request->min_recharge;
                $bank->token = $request->api_key;
                $bank->logo = 'assets/images/momo.svg';
                $bank->save();

                return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
            }
        }

        if ($bank_name === 'MBBank') {
            $valid = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'min_recharge' => 'required|numeric',
                'account_username' => 'required|string',
                'account_password' => 'required|string',
                'api_key' => 'required|string',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $bank = Banking::where('domain', request()->getHost())->where('bank_name', $bank_name)->first();
                $bank->status = $request->status;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->min_recharge = $request->min_recharge;
                $bank->bank_account = $request->account_username;
                $bank->bank_password = $request->account_password;
                $bank->token = $request->api_key;
                $bank->logo = 'assets/images/mbbank.png';
                $bank->save();

                return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
            }
        }

        if ($bank_name === 'Techcombank') {
            $valid = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'min_recharge' => 'required|numeric',
                'account_username' => 'required|string',
                'account_password' => 'required|string',
                'api_key' => 'required|string',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $bank = Banking::where('domain', request()->getHost())->where('bank_name', $bank_name)->first();
                $bank->status = $request->status;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->min_recharge = $request->min_recharge;
                $bank->bank_account = $request->account_username;
                $bank->bank_password = $request->account_password;
                $bank->token = $request->api_key;
                $bank->logo = 'assets/images/techcombank.png';
                $bank->save();

                return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
            }
        }
              
        if ($bank_name === 'ACB') {
            $valid = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'min_recharge' => 'required|numeric',
                'account_username' => 'required|string',
                'account_password' => 'required|string',
                'api_key' => 'required|string',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $bank = Banking::where('domain', request()->getHost())->where('bank_name', $bank_name)->first();
                $bank->status = $request->status;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->min_recharge = $request->min_recharge;
                $bank->bank_account = $request->account_username;
                $bank->bank_password = $request->account_password;
                $bank->token = $request->api_key;
                $bank->logo = 'assets/images/ACB.png';
                $bank->save();

                return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
            }
        }

        if ($bank_name === 'Viettinbank') {
            $valid = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
                'min_recharge' => 'required|numeric',
                'account_username' => 'required|string',
                'account_password' => 'required|string',
                'api_key' => 'required|string',
            ]);

            if ($valid->fails()) {
                return redirect()->back()->with('error', $valid->errors()->first())->withInput();
            } else {
                $bank = Banking::where('domain', request()->getHost())->where('bank_name', $bank_name)->first();
                $bank->status = $request->status;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->min_recharge = $request->min_recharge;
                $bank->bank_account = $request->account_username;
                $bank->bank_password = $request->account_password;
                $bank->token = $request->api_key;
                $bank->logo = 'assets/images/Viettinbank.jpg';
                $bank->save();

                return redirect()->back()->with('success', 'Cập nhật thông tin ngân hàng thành công');
            }
        }
    }

    public function createPromotion(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'min_balance' => 'required|numeric',
            'percentage' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $promotion = new RechargePromotion();
            $promotion->min_balance = $request->min_balance;
            $promotion->percentage = $request->percentage;
            $promotion->status = $request->status;
            $promotion->domain = request()->getHost();
            $promotion->save();

            return redirect()->back()->with('success', 'Tạo khuyến mãi thành công');
        }
    }

    public function viewEditPromotion($id)
    {
        $promotion = RechargePromotion::findOrfail($id);
        return view('admin.payment.edit-promotion', compact('promotion'));
    }

    public function updatePromotion(Request $request, $id)
    {
        $valid = Validator::make($request->all(), [
            'min_balance' => 'required|numeric',
            'percentage' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $promotion = RechargePromotion::find($id);
            $promotion->min_balance = $request->min_balance;
            $promotion->percentage = $request->percentage;
            $promotion->status = $request->status;
            $promotion->save();

            return redirect()->back()->with('success', 'Cập nhật khuyến mãi thành công');
        }
    }

    public function deletePromotion($id)
    {
        $promotion = RechargePromotion::find($id);
        $promotion->delete();

        return redirect()->back()->with('success', 'Xóa khuyến mãi thành công');
    }
}
