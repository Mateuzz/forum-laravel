<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

use App\Lib\LoginThrottler;
use App\Services\UserStatus;
use App\Services\UserToken;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller {
    public function store(StoreLoginRequest $request) {
        $fields = $request->validated();
        $key = $request->ip() . '-' . $fields['email'];
        $loginThrottler = new LoginThrottler($key, 5);

        if (!$loginThrottler->tryAttempt()) {
            $format = $loginThrottler->lockedFor()->forHumans();

            return response([
                'message' => "To many login attemps, your account was locked for $format.",
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        if (auth()->attempt($fields)) {
            $user = $request->user();
            $loginThrottler->clearFailedAttemps();
            session()->regenerate();

            UserStatus::markUserOnline($user);

            return [
                'message' => 'Logged successfully.',
                'user' => $user,
                'token' => UserToken::getPlainTextToken($user)
            ];
        }

        throw ValidationException::withMessages([
            'email' => 'Your credentials could not be verified'
        ]);
    }

    public function index(Request $request) {
        return [
            'message' => 'You are authenticated',
            'user' => $request->user(),
        ];
    }

    public function destroy() {
        /* auth()->logout(); */
        session()->regenerate();
        session()->regenerateToken();

        return response()->json([
            'message' => "Logout sucessfully."
        ]);
    }
}
