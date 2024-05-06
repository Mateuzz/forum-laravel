<?php

namespace App\Lib;

use Illuminate\Support\Facades\Cookie;

class UserIdentifierCookie {
    const USER_IDENTIFIER_COOKIE = 'user-identity';
    const USER_IDENTIFIER_COOKIE_DURATION = 3600 * 24 * 3650;

    private static function generateUserIdentifier() {
        return bin2hex(random_bytes(16));
    }

    public static function get() : string | null {
        return Cookie::get(self::USER_IDENTIFIER_COOKIE);
    }

    public static function queue(): void {
        Cookie::queue(self::USER_IDENTIFIER_COOKIE, self::generateUserIdentifier(), time() + self::USER_IDENTIFIER_COOKIE_DURATION);
    }

    public static function queueIfNotSet(): bool {
        if (Cookie::get(self::USER_IDENTIFIER_COOKIE))
            return false;

        Cookie::queue(self::USER_IDENTIFIER_COOKIE, self::generateUserIdentifier(), time() + self::USER_IDENTIFIER_COOKIE_DURATION);
        return true;
    }
}
