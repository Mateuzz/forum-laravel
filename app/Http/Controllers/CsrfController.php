<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfController extends Controller {
    public function __invoke(Request $request) {
        return [
            'csrf-token' => csrf_token(),
        ];
    }
}
