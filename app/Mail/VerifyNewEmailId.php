<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

use URL;

class VerifyNewEmailId extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $newEmail, $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $newEmail)
    {
        $this->newEmail = $newEmail;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::temporarySignedRoute(
            'user.changeEmail', 
            now()->addHours(config('app.sign_expiry.long')),
            [
                'user'=>$this->user->id,
                'email'=>$this->newEmail
            ]
        );

        return $this->markdown('emails.VerifyNewEmailId',[
                                    'user' => $this->user,
                                    'url' => $url,
                                    'email' => $this->newEmail
                                ])
                                ->subject('Email verification needed to update email address on file');
    }
}
