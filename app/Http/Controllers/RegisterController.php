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

        $fields['status'] = 'online';
        $fields['type'] = 'user';

        $fields['donations']
            = $fields['posts_published']
            = $fields['posts_views_received']
            = $fields['stars_received'] = 0;

        $fields['image'] = null;

        $user = User::create($fields);

        $token = UserToken::getPlainTextToken($user);

        return response([
            'message' => 'User created successfully.',
            'user' => $user,
            'token' => $token,
        ], Response::HTTP_CREATED);
    }
}
