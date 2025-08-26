<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Learner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LearnerRegistrationController extends Controller
{

    public function showLearnerRegistrationPage()
    {
        return view('registration.learnerRegistration');
    }

public function register(Request $request)
{
    // Log incoming request data
    Log::info('Registration request received', $request->all());

    // Validation rules
    $validator = Validator::make($request->all(), [
        'fullName' => 'required|string|min:3|regex:/^[\pL\s]+$/u',
        'email' => 'required|email|unique:russkin_course_cave.learner,email',
        'countryCode' => 'required|string',
        'phoneNumber' => 'required|string|digits_between:8,15|unique:russkin_course_cave.learner,phone',
        'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).+$/',
        'confirmPassword' => 'required|same:password',
        'selectedQuestion' => 'required',
        'questionAnswer' => 'required',
    ], [
        'fullName.required' => 'Full name is required!',
        'fullName.min' => 'Full name must be at least 3 characters!',
        'fullName.regex' => 'Full name should only contain letters and spaces!',
        'email.required' => 'Email is required!',
        'email.email' => 'Invalid email format!',
        'email.unique' => 'This email is already registered!',
        'countryCode.required' => 'Country code is required!',
        'phoneNumber.required' => 'Phone number is required!',
        'phoneNumber.numeric' => 'Phone number must contain only digits!',
        'phoneNumber.digits_between' => 'Phone number must be between 8 and 15 digits!',
        'phoneNumber.unique' => 'This phone number is already registered!',
        'password.required' => 'Password is required!',
        'password.min' => 'Password must be at least 8 characters!',
        'password.regex' => 'Password must contain an uppercase letter, a number, and a special character!',
        'confirmPassword.required' => 'Please confirm your password!',
        'confirmPassword.same' => 'Passwords do not match!',
        'selectedQuestion.required' => 'Security question must be selected!',
        'questionAnswer.required' => 'Answer to the security question is required!',
    ]);

    if ($validator->fails()) {
        Log::warning('Registration validation failed', [
            'errors' => $validator->errors()->toArray()
        ]);

        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
        ], 422);
    }

    try {
        $learner = new Learner();
        $learner->secret_id = rand(1000000000, 9999999999);
        $learner->name = $request->fullName;
        $learner->email = $request->email;
        $learner->phone = $request->countryCode . $request->phoneNumber;
        $learner->password = Hash::make($request->password);
        $learner->question = $request->selectedQuestion;
        $learner->answer = $request->questionAnswer;
        $learner->save();

        Log::info('Learner registered successfully', ['email' => $learner->email]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Now Login!',
        ], 201);
    } catch (\Exception $e) {
        Log::error('Error during learner registration', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again later.',
        ], 500);
    }
}

}
