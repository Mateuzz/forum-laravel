<?php

namespace App\Services;

use App\Models\User;

class UserStatus {
    public static $minutesBeforeOffline = 5;

    static public function markUserActivity(User $user): void {
        User::where('id', $user->id)
            ->update(['last_activity' => now()]);
    }

    static public function markUserOnline(User $user): void {
        User::where('id', $user->id)
            ->update([
                'last_activity' => now(),
                'status' => 'online',
            ]);
    }

    static public function markAllInactiveUsersAsOffline(): void {
        $time = now()->subMinutes(self::$minutesBeforeOffline);

        User::where('last_activity', '<', $time)
            ->update(['status' => 'offline']);
    }
}
