<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UserRegistered;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;
    public $tries = 3; //threshold of how many times it should try to execute the job
    public $backoff = 10; //waits after every try
    /**
     * Create the event listener.
     */
    public function __construct(public User $user)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        Mail::to($user)->send(new WelcomeUser($user));
    }
}
