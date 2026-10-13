<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(protected Request $request)
    {
    }

    /**
     * Persist last login on user_details (columns do not exist on users table).
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $user->user_detail()->update([
            'last_login_at' => now()->toDateTimeString(),
            'last_login_ip' => $this->request->ip(),
        ]);
    }
}
