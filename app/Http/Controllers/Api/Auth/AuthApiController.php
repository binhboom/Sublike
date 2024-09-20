<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserApiResource;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    public function doLogin(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required|string|min:6',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $valid->errors()->first()
            ]);
        } else {
            $user = User::where('username', $request->username)->where('domain', $request->getHost())->first();
            if ($user) {

                if ($user->status !== 'active') {
                    return response()->json([
                        'status' => 'error',
                        'code' => 400,
                        'message' => "Tài khoản của bạn đã bị khoá vui lòng liên hệ Admin để biết chi tiết!"
                    ]);
                }

                if (Hash::check($request->password, $user->password)) {
                    $login_expired_at = now()->addMinutes(60);
                    $access_login = encrypt($user->username . '|' . $user->domain . '|' . 'logged' . '|' . time());
                    $user->login_expired_at = $login_expired_at;
                    $user->access_login = $access_login;
                    $user->last_login = now();
                    $user->last_ip = $request->getClientIp();
                    $user->save();

                    UserActivity::create([
                        'user_id' => $user->id,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'activity' => 'auth',
                        'note' => 'Đăng nhập thành công, địa chỉ IP: ' . $request->ip(),
                        'domain' => request()->getHost(),
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'code' => 201,
                        'message' => "Đăng nhập tài khoản thành công!",
                        'data' => [
                            'access_login' => $access_login,
                            'login_expired_at' => $login_expired_at
                        ],
                        'user' => new UserApiResource($user)
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'code' => 400,
                        'message' => "Mật khẩu không đúng vui lòng thử lại"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Tài khoản đăng nhập không tồn tại"
                ]);
            }
        }
    }

    public function doRegister(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string|min:6|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:6|max:255',
            'password' => 'required|string|min:6',
            'confirmPassword' => 'required|string|same:password',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $valid->errors()->first()
            ]);
        } else {
            $api_token = encrypt($request->email . '|' . $request->username . '|' . request()->getHost() . '|' . now());

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'balance' => 0,
                'total_recharge' => 0,
                'api_token' => $api_token,
                'domain' => request()->getHost(),
            ]);

            if ($user) {

                UserActivity::create([
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'activity' => 'auth',
                    'note' => 'Tạo tài khoản thành công, địa chỉ IP: ' . $request->ip(),
                    'domain' => request()->getHost(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'code' => 201,
                    'message' => "Đăng kí tài khoản thành công!",
                    'redirect' => '/auth/login'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Đăng kí tài khoản thất bại!"
                ]);
            }
        }
    }
}
