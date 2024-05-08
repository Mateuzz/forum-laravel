<?php

namespace App;

use App\Models\Category;
use App\Models\PostUserView;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class Test {
    static public function t()
    {
        /** @var Collection<User> */
        $fields['email'] = 'novoodadssd';
        $fields['password'] = 'interessnate';
        $fields['username'] = 'Outro nome';

        /* User::create($fields); */

        $user = User::whereEmail($fields['email'])
            ->get();

        $userPassd = $user->all()[0]->password;
        print_r(Hash::check($fields['password'], $userPassd));
        /* if (Hash::check($fields['password'], $user->first->password)) { */
        /*     echo "ok"; */
        /* } else { */
        /*     echo "false"; */
        /* } */
    }
}

?>
