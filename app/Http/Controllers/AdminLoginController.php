<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function showLoginPage()
    {
        return view('login.adminLogin');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $admin = Admin::where('email', $request->email)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials. Try again.',
                ], 401);
            }

            Auth::guard('admin')->login($admin);

            $ip = $request->ip();

            try {
                Activity::create([
                    'message' => '<strong class="text-gray-900">'.e($admin->name).'</strong> ('.e($admin->email).') <span class="text-green-600 font-semibold">logged in</span> to the admin panel from IP <code class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-800 font-mono text-[0.85em]">'.e($request->ip()).'</code>.'

                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to write admin login activity', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful! Welcome, Admin!',
                'redirect' => route('admin.dashboard'),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Admin login error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

   public function logout(Request $request)
        {
            if ($admin = Auth::guard('admin')->user()) {
                Activity::create([
                    'message' => '<strong class="text-gray-900">'.e($admin->name).'</strong> ('.e($admin->email).') <span class="text-red-600 font-semibold">logged out</span> of the admin panel.'
                ]);
            }
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('message', 'You have been logged out successfully!');
        }

    public function showDashboard()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login')->with('message', 'Please log in to access the dashboard.');
        }

        return view('lms.admin', compact('admin'));
    }
}