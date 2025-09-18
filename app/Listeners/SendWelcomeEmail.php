<?php

namespace App\Listeners;

use App\Mail\UserRegisteredMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        try {
            Mail::to($event->user->email)->send(new UserRegisteredMail($event->user));
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email to user ' . $event->user->id . ': ' . $e->getMessage());
        }
    }
}
