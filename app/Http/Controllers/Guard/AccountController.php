<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\RechargeCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function changePassword(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $user = Auth::user();

            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();

                return redirect()->back()->with('success', __('Password has been changed'));
            } else {
                return redirect()->back()->with('error', __('Current password is invalid'))->withInput();
            }
        }
    }

    public function twoFactorAuth(Request $request)
    {

        if (Auth::user()->two_factor_auth === 'yes') {
            return redirect()->back()->with('error', __('Two factor authentication has been enabled'));
        }

        $valid = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $ga = new \App\Library\GoogleAuthenticator();
            $user = Auth::user();

            if ($ga->verifyCode($user->two_factor_secret, $request->code, 2)) {
                $user->two_factor_auth = 'yes';
                $user->save();

                return redirect()->back()->with('success', __('Two factor authentication has been enabled'));
            } else {
                return redirect()->back()->with('error', __('Code is invalid'))->withInput();
            }
        }
    }

    public function twoFactorAuthDisable(Request $request)
    {
        if (Auth::user()->two_factor_auth !== 'yes') {
            return redirect()->back()->with('error', __('Two factor authentication has been disabled'));
        }

        $valid = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $ga = new \App\Library\GoogleAuthenticator();
            $user = Auth::user();

            if ($ga->verifyCode($user->two_factor_secret, $request->code, 2)) {
                $user->two_factor_auth = 'no';
                $user->two_factor_secret = null;
                $user->save();

                return redirect()->back()->with('success', __('Two factor authentication has been disabled'));
            } else {
                return redirect()->back()->with('error', __('Code is invalid'))->withInput();
            }
        }
    }

    public function reloadUserToken()
    {
        $user = Auth::user();
        $api_token = encrypt($user->email . '|' . $user->username . '|' . request()->getHost() . '|' . now());
        $user->api_token = $api_token;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Token has been reloaded',
            'api_token' => $api_token
        ]);
    }

    public function updateStatusTelegram(Request $request)
    {

        $user = Auth::user();
        if ($user->telegram_id === null) {
            return redirect()->back()->with(
                'error',
                __('You have not verified your telegram account')
            )->withInput();
        }
        $user->notification_telegram = $request->status === 'yes' ? 'yes' : 'no';
        $user->save();

        return redirect()->back()->with('success', __('Status has been updated'));
    }

    public function rechargeCard(Request $request){
        $valid = Validator::make($request->all(), [
            'card_type' => 'required|string',
            'card_value' => 'required|integer',
            'card_seri' => 'required|string',
            'card_code' => 'required|string',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {
            $partner_id = site('partner_id');
            $partner_key = site('partner_key');
            $percent_card = site('percent_card'); // phần trăm chiết khấu
            $sign = md5($partner_key . $request->card_code . $request->card_seri);
            $request_id = time() . rand(100000, 999999);
            $result = thecao($request->card_type, $request->card_code, $request->card_seri, $request->card_value, $request_id, $partner_id, $sign);
            if(isset($result) && $result->status == 99){
                $trans_id = $result->trans_id;
                // $amount = $result->amount;
                $amount = $request->card_value;

                // tính phần trăm chiết khấu
                $real_amount = $amount - ($amount * $percent_card / 100);

                RechargeCard::create([
                    'code' => $result->request_id,
                    'user_id' => Auth::id(),
                    'type' => $request->card_type,
                    'amount' => $request->card_value,
                    'real_amount' => $real_amount,
                    'serial' => $request->card_seri,
                    'pin' => $request->card_code,
                    'status' => 'pending',
                    'tran_id' => $trans_id,
                    'note' => 'Nạp thẻ thẻ cào',
                    'domain' => request()->getHost(),
                ]);

                return redirect()->back()->with('success', 'Yêu cầu nạp thẻ đã được gửi');

            }else{
                return redirect()->back()->with('error', $result->message)->withInput();
            }
        }
    }

    public function checkLevel(Request $request){
        $user = Auth::user();
        $level = $user->level;
        if($level == 'member'){
            if($user->total_recharge >= site('collaborator')){
                $user->level = 'collaborator';
                $user->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn đã lên cấp bậc Cộng tác viên',
                ]);
            }
        }

        if($level == 'collaborator'){
            if($user->total_recharge >= site('agency')){
                $user->level = 'agency';
                $user->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn đã lên cấp bậc Đại lý',
                ]);
            }
        }

        if($level == 'agency'){
            if($user->total_recharge >= site('distributor')){
                $user->level = 'distributor';
                $user->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn đã lên cấp bậc Nhà phân phối',
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Dữ liệu đang cập nhật',
        ]);
    }
}
