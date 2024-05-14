<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    protected const DEFAULT_PAGES_COUNT = 20;
    protected const MAX_PAGES_COUNT = 100;

    public function index(Request $request) {
        $validated = $request->validate([
            'search' => 'string|nullable',
            'results-per-page' => 'numeric|nullable',
            'order' => ['nullable', Rule::in(User::ALLOWED_ORDER_TYPES)],
        ]);

        $pagesCount = min(self::MAX_PAGES_COUNT, $validated['results-per-page'] ?? self::DEFAULT_PAGES_COUNT);

        return User::order($validated['order'] ?? null)
                  ->filter($validated)
                  ->paginate($pagesCount);
    }

    public function show(User $user) {
        return $user;
    }
}
