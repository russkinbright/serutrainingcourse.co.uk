<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Learner;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class LearnerLoginController extends Controller
{
    public function showLearnerLoginPage()
    {
        return view('login.learnerLogin');
    }

  public function login(Request $request)
{
    try {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $learner = Learner::where('email', $request->email)->first();

        if (!$learner || !\Hash::check($request->password, $learner->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials. Try again.'], 401);
        }

        // what token (if any) is already stored for this user?
        $existingToken = $learner->login_token;
        // what token (if any) is the current browser already holding?
        $cookieToken   = $request->cookie('login_token');

        // If a token exists in DB and it doesn't match this browser's cookie,
        // the account is already logged in somewhere else.
        if ($existingToken && $existingToken !== $cookieToken) {
            // OPTIONAL: allow a "force" login by passing ?force=1 to kick out other devices
            if ($request->boolean('force')) {
                // rotate to a fresh token and proceed; the middleware will log out other devices
                $existingToken = (string) Str::uuid();
                $learner->login_token = $existingToken;
                $learner->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already logged in on another device/another tab. Log out there. At a time you can login into one device',
                    'requires_force' => true
                ], 423); // 423 Locked (or 409 Conflict)
            }
        }

        // If there is no token yet (first login ever or after logout), create one.
        if (!$existingToken) {
            $existingToken = (string) Str::uuid();
            $learner->login_token = $existingToken;
            $learner->save();
        }

        // Set/refresh cookie to the *existing* token (do NOT generate a new one here).
        Cookie::queue(
            Cookie::make(
                name: 'login_token',
                value: $existingToken,
                minutes: 60 * 24 * 30,
                path: '/',
                domain: null,       // use null unless you truly need a parent domain cookie
                secure: true,
                httpOnly: true,
                raw: false,
                sameSite: 'Lax'
            )
        );

        Auth::guard('learner')->login($learner);

        $redirectUrl = $request->input('redirect') ?: route('learner.page');

        return response()->json([
            'success'  => true,
            'message'  => 'Login successful! Welcome back!',
            'redirect' => $redirectUrl,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Login error:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()], 500);
    }
}

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $learner = Learner::where('email', $request->email)->first();

        return response()->json([
            'exists' => !!$learner,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
            $learner = Learner::where('email', $request->email)->first();

            if (!$learner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found.',
                ], 404);
            }

            $token = Str::random(60);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );

            $resetLink = "https://serutrainingcourse.co.uk/password?email=" . urlencode($request->email) . "&token=" . $token;
            Mail::to($request->email)->send(new PasswordResetMail($resetLink));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email!',
            ]);
        } catch (\Exception $e) {
            Log::error('Forgot password error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error sending reset link: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showResetPasswordPage(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$resetToken || now()->diffInMinutes($resetToken->created_at) > 60) {
            return redirect()->route('learner.learnerLogin')->with('message', 'Invalid or expired reset link.');
        }

        return view('login.forgetPassword', compact('email', 'token'));
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$resetToken || now()->diffInMinutes($resetToken->created_at) > 60) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token.',
                ], 400);
            }

            $learner = Learner::where('email', $request->email)->first();
            if (!$learner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not found.',
                ], 404);
            }

            $learner->password = Hash::make($request->password);
            $learner->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! Redirecting to login...',
            ]);
        } catch (\Exception $e) {
            Log::error('Reset password error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error resetting password: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
        {
            $learner = Auth::guard('learner')->user();
            if ($learner) {
                $learner->login_token = null; // ✅ clear token
                $learner->save();
            }

            Auth::guard('learner')->logout();
            Cookie::queue(Cookie::forget('login_token'));

            return redirect()->route('learner.learnerLogin')->with('message', 'You have been logged out successfully!');
        }


    public function showLearnerPage()
    {
        $learner = Auth::guard('learner')->user();
        if (!$learner) {
            return redirect()->route('learner.learnerLogin')->with('message', 'Please log in to access your dashboard.');
        }

        $courses = [
            [
                'name' => 'Graphic Design Bootcamp: Advanced Training Using Adobe CCas',
                'total_enrollment' => 10,
                'rating' => 4.0,
            ],
        ];

        return view('learner.learnerPage', compact('learner', 'courses'));
    }
}