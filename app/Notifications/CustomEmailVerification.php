<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomEmailVerification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to Canteen Central - Verify Your Email')
            ->view('emails.verify-email', [
                'user' => $notifiable, 
                'url' => $this->verificationUrl($notifiable)
            ]);
    }
}