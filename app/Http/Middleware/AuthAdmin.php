<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('admin_secret_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }
        return $next($request);
    }
}