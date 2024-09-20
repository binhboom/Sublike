<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    // Đăng nhập bằng Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if($existingUser){
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => encrypt('password123') // Mật khẩu giả định
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Đăng nhập Google thất bại, vui lòng thử lại.');
        }
    }

    // Đăng nhập bằng Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $existingUser = User::where('email', $facebookUser->getEmail())->first();

            if($existingUser){
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                    'password' => encrypt('password123') // Mật khẩu giả định
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Đăng nhập Facebook thất bại, vui lòng thử lại.');
        }
    }
}
