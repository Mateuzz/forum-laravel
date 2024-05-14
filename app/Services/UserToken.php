<?php

namespace App\Services;

use App\Models\User;

class UserToken {
    static public function getPlainTextToken(User $user): string {
        $user->tokens()->delete();
        return $user->createToken('user-auth-token')->plainTextToken;
    }
}
