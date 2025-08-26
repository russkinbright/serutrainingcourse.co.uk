<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ViewProfileController extends Controller
{
    public function show()
    {
        $learner = Auth::guard('learner')->user();
        if (!$learner) {
            return redirect()->route('learner.learnerLogin')->with('error', 'Please log in to view your profile.');
        }
        return view('learner.viewProfile', compact('learner'));
    }

    public function updateProfile(Request $request)
    {
        $learner = Auth::guard('learner')->user();
        if (!$learner) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Please log in to update your profile.'], 401)
                : redirect()->route('learner.learnerLogin')->with('error', 'Please log in to update your profile.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:learner,email,' . $learner->id,
            'card' => 'nullable|numaric|max:255',
            'card_expiry' => 'nullable|string|max:255',
            'card_code' => 'nullable|numaric|max:255',
            'phone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $request->expectsJson()
                ? response()->json(['message' => $validator->errors()->first()], 422)
                : redirect()->back()->withErrors($validator)->withInput();
        }

        $learner->update($request->only([
            'name', 'email', 'card', 'card_expiry', 'card_code',
            'phone', 'country', 'city', 'address', 'postal_code'
        ]));

        return $request->expectsJson()
            ? response()->json(['message' => 'Profile updated successfully.'])
            : redirect()->route('learner.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $learner = Auth::guard('learner')->user();
        if (!$learner) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Please log in to update your password.'], 401)
                : redirect()->route('learner.learnerLogin')->with('error', 'Please log in to update your password.');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/',
        ]);

        if ($validator->fails()) {
            return $request->expectsJson()
                ? response()->json(['message' => $validator->errors()->first()], 422)
                : redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $learner->password)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Current password is incorrect.'], 422)
                : redirect()->back()->with('error', 'Current password is incorrect.')->withInput();
        }

        $learner->update([
            'password' => Hash::make($request->password),
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Password updated successfully.'])
            : redirect()->route('learner.profile.show')->with('success', 'Password updated successfully.');
    }
}