<?php

use App\Services\UserStatus;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn() =>
    UserStatus::markAllInactiveUsersAsOffline()
)->everyFiveMinutes();
