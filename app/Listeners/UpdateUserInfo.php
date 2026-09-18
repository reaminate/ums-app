<?php

namespace App\Listeners;

use App\Events\UserInfoUpdated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserInfo
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserInfoUpdated $event): void
    {
        $user = $event->user;
        $changes = $event->changes;

        User::findOrFail($user->id)->update($changes);
    }
}
