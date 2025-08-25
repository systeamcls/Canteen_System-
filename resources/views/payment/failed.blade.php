@extends('layouts.canteen')

@section('title', 'Payment Failed - LTO Canteen Central')

@section('content')
<div style="padding: 120px 0 60px; background: #fef2f2; min-height: 100vh;">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto; text-align: center;">
            <!-- Error Icon -->
            <div style="width: 100px; height: 100px; background: #ef4444; border-radius: 50%; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 50px; height: 50px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>

            <!-- Error Message -->
            <h1 style="color: #dc2626; font-size: 2.5rem; margin-bottom: 15px;">Payment Failed</h1>
            <p style="color: #b91c1c; font-size: 1.2rem; margin-bottom: 30px;">
                We couldn't process your payment. Don't worry, no charges were made.
            </p>

            <!-- Common Reasons -->
            <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: left; border: 1px solid #fecaca;">
                <h3 style="color: #dc2626; margin-bottom: 15px;">Common reasons for payment failure:</h3>
                <ul style="color: #991b1b; line-height: 1.6;">
                    <li>Insufficient funds in your account</li>
                    <li>Incorrect payment details</li>
                    <li>Payment method not supported</li>
                    <li>Bank or network timeout</li>
                    <li>Transaction limit exceeded</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 30px;">
                <a href="{{ route('checkout') }}" style="background: #ef4444; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Try Again
                </a>
                <a href="{{ route('menu.index') }}" style="background: #6b7280; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Back to Menu
                </a>
            </div>

            <!-- Help Information -->
            <div style="background: white; border-radius: 12px; padding: 20px; text-align: left; border: 1px solid #fecaca;">
                <h3 style="color: #dc2626; margin-bottom: 15px;">Need help?</h3>
                <p style="color: #991b1b; margin-bottom: 15px;">
                    If you continue to have issues with payment, please try:
                </p>
                <ul style="color: #991b1b; line-height: 1.6; margin-bottom: 15px;">
                    <li>Using a different payment method</li>
                    <li>Checking your internet connection</li>
                    <li>Contacting your bank</li>
                    <li>Trying again in a few minutes</li>
                </ul>
                <p style="color: #991b1b;">
                    <strong>Still having trouble?</strong> Contact our support team at 
                    <a href="mailto:support@ltocanteen.com" style="color: #dc2626;">support@ltocanteen.com</a> 
                    or call us at <strong>(123) 456-7890</strong>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection