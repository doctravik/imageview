<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ActivationTokenSent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendActivationEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \Illuminate\Auth\Events\Registered | \App\Events\UserRequestedActivationEmail $event
     * @return void
     */
    public function handle($event)
    {
        $event->user->notify(new ActivationTokenSent(
            $event->user->name,
            $event->user->generateActivationToken()
        ));
    }
}
