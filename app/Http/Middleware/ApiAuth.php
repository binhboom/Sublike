<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);

        try {

            $accessToken = $request->bearerToken();
            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'code' => '400',
                    'message' => "Bạn chưa đăng nhập!"
                ]);
            }
            $user = User::where('access_login', $accessToken)->where('login_expired_at', '>', now())->where('domain', site('domain'))->first();
            if ($user) {
                $request->merge(['user' => $user]);
                return $next($request);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Tài khoản không tồn tại!"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
