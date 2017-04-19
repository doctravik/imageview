<?php

namespace App\Notifications;

use App\ActivationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ActivationTokenSent extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    public $username;

    /**
     * @var ActivationToken
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($username, ActivationToken $token)
    {
        $this->username = $username;

        $this->token = $token;
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('account.activate', $this->token->token);

        return (new MailMessage)
                    ->subject('Activate your account')
                    ->line('Please activate your account by clicking the button below.')
                    ->action('Activate your account', $url)
                    ->line('Thank you for using our application!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
