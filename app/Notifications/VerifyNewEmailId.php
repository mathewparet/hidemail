<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\URL;

use App\User;

class VerifyNewEmailId extends Notification implements ShouldQueue
{
    use Queueable;

    private $newEmail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newEmail)
    {
        $this->newEmail = $newEmail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $user
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($user)
    {
        $url = URL::temporarySignedRoute(
            'user.changeEmail', 
            now()->addHours(config('app.sign_expiry.long')),
            [
                'user'=>$user->id,
                'email'=>$this->newEmail
            ]
        );

        return (new MailMessage)->markdown('emails.VerifyNewEmailId', [
                                    'user' => $user,
                                    'url' => $url,
                                    'email' => $this->newEmail
                                ])
                                ->subject('Email verification needed to update email address on file');
    }
}
