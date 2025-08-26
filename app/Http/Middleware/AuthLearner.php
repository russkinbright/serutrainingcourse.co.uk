<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;   // ✅ add this
use Illuminate\Support\Facades\Cookie;

class AuthLearner
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('learner')->check()) {
            $learner = Auth::guard('learner')->user();
            $cookieToken = Cookie::get('login_token');

            if (!$cookieToken || $learner->login_token !== $cookieToken) {
                Auth::guard('learner')->logout();
                Cookie::queue(Cookie::forget('login_token'));

                return redirect()->route('learner.learnerLogin')->withErrors('You were logged out because your account was used on another device.');
            }
        }

        return $next($request);
    }
}