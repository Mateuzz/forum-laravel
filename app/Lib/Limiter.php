<?php

namespace App\Lib;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

enum LimiterHitResult {
    case WasLocked;
    case HitAndLocked;
    case Hit;
}

class Limiter {
    protected string $lockKey;
    protected string $remainingKey;

    function __construct(string $key, public int $maxAttempts) {
        $this->lockKey = "$key.locked-until";
        $this->remainingKey = "$key.remaining";
    }

    protected function lock(int $duration): void {
        $now = now()->addSeconds($duration);
        Cache::put($this->lockKey, $now->format(DATE_RFC3339), $duration);
    }

    public function attempsRemaining(): int {
        return Cache::get($this->remainingKey, $this->maxAttempts);
    }

    public function lockedFor() : CarbonInterval | null {
        $lockedUntil = Cache::get($this->lockKey);

        if (!$lockedUntil)
            return null;

        $lockedUntil = Carbon::createFromFormat(DATE_RFC3339, $lockedUntil);

        return $lockedUntil->diff(now());
    }

    public function isLocked(): bool {
        return Cache::has($this->lockKey);
    }

    public function reset(): void {
        Cache::forget($this->lockKey);
        Cache::forget($this->remainingKey);
    }

    public function hit(int $throttleTime): LimiterHitResult {
        $attempsRemaining = $this->attempsRemaining();

        if (!$attempsRemaining) {
            return LimiterHitResult::WasLocked;
        }

        if ($attempsRemaining === $this->maxAttempts) {
            Cache::put($this->remainingKey, $attempsRemaining - 1, $throttleTime);
        } else {
            Cache::decrement($this->remainingKey);
        }

        --$attempsRemaining;

        if (!$attempsRemaining) {
            $this->lock($throttleTime);
            return LimiterHitResult::HitAndLocked;
        }

        return LimiterHitResult::Hit;
    }
}


?>
