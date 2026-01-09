<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAccActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build()
    {
        $activationUrl = config('app.frontend_url')
        . '/contact/account-activation/'
        . $this->user->email_token;

        return $this->subject('Aktivasi Akun Anda')
            ->view('emails.account_activation')
            ->with([
                'activationUrl' => $activationUrl,
            ]);
    }
}
