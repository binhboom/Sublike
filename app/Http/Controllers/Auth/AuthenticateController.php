<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ConfigSite;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthenticateController extends Controller
{

    public function viewLogin()
    {
        return view('auth.login');
    }

    public function viewRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        if (session()->has('two_factor_auth')) {
            if (now() > session('two_factor_auth.time')) {
                $request->session()->forget('two_factor_auth');
                return redirect()->back()->with('error', __('Time is up, please try again'));
            }

            $valid = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
                'code' => 'required|string',
            ]);
        } else {
            $valid = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
        }

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
        } else {
            $user = User::where('username', $request->username)->where('domain', $request->getHost())->first();

            if ($user) {

                // if($user->role !== 'admin') {
                //     return redirect()->back()->with('error', __('Đăng nhập thất bại'));
                // }

                if ($user->status === 'banned') {
                    return redirect()->back()->with('error', __('Your account has been banned'));
                }

                if ($user->two_factor_auth === 'yes') {
                    if (!session()->has('two_factor_auth')) {
                        $request->session()->put('two_factor_auth', [
                            'username' => $request->username,
                            'time' => now()->addMinutes(3),
                        ]);

                        return redirect()->back()->with('info', __('Please enter the code from your authenticator app'))->withInput([
                            'username' => $request->username,
                        ]);
                    }
                }

                if (Hash::check($request->password, $user->password)) {
                    if (session()->has('two_factor_auth')) {
                        $ga = new \App\Library\GoogleAuthenticator();
                        if ($ga->verifyCode($user->two_factor_secret, $request->code, 2)) {
                            $request->session()->forget('two_factor_auth');
                        } else {
                            return redirect()->back()->with('error', __('Code is invalid'))->withInput([
                                'username' => $request->username,
                            ]);
                        }
                    }
                    Auth::login($user, $request->remember);

                    Auth::logoutOtherDevices($request->password);

                    $user->last_login = now();
                    $user->last_ip = $request->ip();
                    $user->save();

                    $userActivity = UserActivity::where('user_id', $user->id)->get();
                    if ($userActivity->count() > 10) {
                        $userActivity->first()->delete();
                    }

                    UserActivity::create([
                        'user_id' => $user->id,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'activity' => 'auth',
                        'note' => 'Đăng nhập thành công, địa chỉ IP: ' . $request->ip(),
                        'domain' => request()->getHost(),
                    ]);

                    // return redirect()->route('admin.dashboard')->with('success', __('You have been logged in') . ' ' . $user->name);
                    return redirect()->route('home')->with('success', __('You have been logged in') . ' ' . $user->name);
                } else {
                    return redirect()->back()->with('error', __('Invalid username or password'));
                }
            } else {
                return redirect()->back()->with('error', __('Invalid username or password'));
            }
        }
    }

    public function register(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:6',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first());
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

                return redirect()->route('login')->with('success', __('Account created successfully'))->withInput([
                    'username' => $request->username,
                ]);
            } else {
                return redirect()->back()->with('error', __('An error occurred while creating your account'));
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('login')->with('success', __('You have been logged out'));
    }

    public function viewInstall()
    {
        return view('auth.install');
    }

    public function install(Request $request)
    {
        $site = ConfigSite::where('domain', $request->getHost())->first();

        if ($site && $site->status === 'active') {
            return redirect()->route('login');
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:6|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->with('error', $valid->errors()->first())->withInput();
        } else {

            if (env('APP_MAIN_SITE') !== $request->getHost()) {
                if (empty($request->api_token)) {
                    return redirect()->back()->with('error', __('API Token is required'));
                }

                $userMain = User::where('api_token', $request->api_token)->first();
                if (!$userMain) {
                    return redirect()->back()->with('error', __('API Token is invalid'));
                }

                $siteMain = ConfigSite::where('domain', $request->getHost())->first();
                if ($siteMain) {
                    $siteMain->delete();
                }

                $token = Str::random(60);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'balance' => 0,
                    'total_recharge' => 0,
                    'domain' => request()->getHost(),
                    'role' => 'admin',
                    'api_token' => $token,
                ]);


                $siteMain = ConfigSite::create([
                    'name_site' => $request->getHost(),
                    'admin_username' => $userMain->username,
                    'site_token' => $token,
                    'is_domain' => $userMain->domain,
                    'domain' => $request->getHost(),
                    'status' => 'active',
                ]);

                if ($siteMain) {
                    return redirect()->route('login')->with('success', __('Account created successfully'))->withInput([
                        'username' => $request->username,
                    ]);
                } else {
                    return redirect()->back()->with('error', __('An error occurred while creating your account'));
                }
            } else {

                $api_token = encrypt($request->email . '|' . $request->username . '|' . request()->getHost() . '|' . now());

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'balance' => 0,
                    'total_recharge' => 0,
                    'domain' => request()->getHost(),
                    'role' => 'admin',
                    'api_token' => $api_token,
                ]);

                if ($user) {

                    $api_token = Str::random(60);

                    $siteMain = ConfigSite::create([
                        'name_site' => $request->getHost(),
                        'admin_username' => $request->username,
                        'site_token' => $api_token,
                        'is_domain' => $request->getHost(),
                        'domain' => $request->getHost(),
                        'status' => 'active',
                    ]);

                    UserActivity::create([
                        'user_id' => $user->id,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'activity' => 'auth',
                        'note' => 'Tạo tài khoản quản trị thành công, địa chỉ IP: ' . $request->ip(),
                        'domain' => request()->getHost(),
                    ]);

                    if ($siteMain) {
                        return redirect()->route('login')->with('success', __('Account created successfully'))->withInput([
                            'username' => $request->username,
                        ]);
                    } else {
                        return redirect()->back()->with('error', __('An error occurred while creating your account'));
                    }
                } else {
                    return redirect()->back()->with('error', __('An error occurred while creating your account'));
                }
            }
        }
    }
}
