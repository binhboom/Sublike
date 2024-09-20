<?php

namespace App\Http\Middleware;

use App\Models\ConfigSite;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);

        if (env('APP_MAIN_SITE') === $request->getHost()) {

            $site =  ConfigSite::where('domain', $request->getHost())->first();

            if ($site && $site->status === 'active') {

                if ($request->path() === 'site/install') {
                    return abort(404);
                }

                return $next($request);
            } else {

                if ($site) {
                    $site->delete();
                }

                if (Auth::check()) {
                    Auth::logout();
                }

                if ($request->path() === 'site/install') {
                    return $next($request);
                } else {
                    return redirect()->route('install');
                }
            }
        } else {
            $site =  ConfigSite::where('domain', $request->getHost())->first();

            if ($site && $site->status === 'active') {

                if ($request->path() === 'site/install') {
                    return abort(404);
                }

                return $next($request);
            } else {

                if ($site) {
                    $site->delete();
                }

                if (Auth::check()) {
                    Auth::logout();
                }

                if ($request->path() === 'site/install') {
                    return $next($request);
                } else {
                    return redirect()->route('install');
                }
            }
        }
    }
}
