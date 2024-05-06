<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Services\UserToken;

class RegisterController extends Controller {
    public function store(Request $request) {
        $fields = $request->validate([
            'username' => 'required|max:80|min:2',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:14|max:255',
        ]);

        $user = User::create($fields);
        auth()->login($user);
        session()->regenerate();

        $token = UserToken::getPlainTextToken($user);

        return response([
            'message' => 'User created successfully.',
            'user' => $request->user(),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }
}
