<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class EmailVerificationController extends Controller
{
    public function notice()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('user.profile.show');
        }
        
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
{
    $request->fulfill();
    
    // Re-authenticate the user and redirect to profile
    Auth::login($request->user());
    session(['user_type' => 'employee']);
    
        return redirect()->route('verification.success');

}

    public function resend(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('user.profile.show');
        }
        
        if (!$user->canResendVerification()) {
            return back()->with('error', 'Please wait 60 seconds before requesting another verification email.');
        }
        
        $user->sendEmailVerificationNotification();
        $user->markVerificationSent();
        
        return back()->with('success', 'Verification email sent!');
    }

    public function success()
{
    return view('auth.verification-success');
}

    /**
     * Check verification status (for AJAX polling)
     */
    public function checkStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'verified' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        return response()->json([
            'authenticated' => true,
            'verified' => $user->hasVerifiedEmail(),
            'email' => $user->email,
            'needs_verification' => !$user->hasVerifiedEmail(),
        ]);
    }
}