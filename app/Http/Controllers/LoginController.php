<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

use App\Lib\LoginThrottler;
use App\Models\User;
use App\Services\UserStatus;
use App\Services\UserToken;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller {
    public function store(StoreLoginRequest $request) {
        $fields = $request->validated();
        $key = "ip-" . $request->ip() . ".email-" . $fields['email'];
        $loginThrottler = new LoginThrottler($key, 5);

        if (!$loginThrottler->tryAttempt()) {
            $format = $loginThrottler->lockedFor()->forHumans();

            return response([
                'message' => "To many login attemps, your account was locked for $format.",
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        /** @var User | null */
        $user = User::whereEmail($fields['email'])
                    ->first();

        if ($user && Hash::check($fields['password'], $user->password)) {
            $loginThrottler->reset();

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

    /* public function destroy() { */
    /*     /1* auth()->logout(); *1/ */
    /*     session()->regenerate(); */
    /*     session()->regenerateToken(); */

    /*     return response()->json([ */
    /*         'message' => "Logout sucessfully." */
    /*     ]); */
    /* } */
}
