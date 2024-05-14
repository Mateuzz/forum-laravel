<?php

namespace App\Lib;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

class ThrottleTimerCalculator {
    public function __invoke(int $lockedSequenceNumber) {
        return pow(2, $lockedSequenceNumber + 1) * 60;
    }
}

class LoginThrottler {
    protected Limiter $limiter;
    protected string $key;
    private string $lockedSequenceKey;

    function __construct(
            string $key,
            int $maxAttempts = 5,
            public ThrottleTimerCalculator $throttleTimerCalculator = new ThrottleTimerCalculator) {
        $this->limiter = new Limiter(key: "login-throttler-limiter-$key", maxAttempts: $maxAttempts);
        $this->key = "login-throttler-$key";
        $this->lockedSequenceKey = "{$this->key}-locked-sequence";
    }

    private function increaseThrottleTime(): void {
        $lockedSequenceNumber = $this->getLockedSequenceNumber();
        $oldThrottleTime = ($this->throttleTimerCalculator)($lockedSequenceNumber);
        $newThrottleTime = ($this->throttleTimerCalculator)($lockedSequenceNumber + 1);

        // the throttle time for the next lock will reset after the equivalent throttle time has passed after this unlocking
        Cache::put($this->lockedSequenceKey, $lockedSequenceNumber + 1, $oldThrottleTime + $newThrottleTime);
    }

    private function resetThrottleTime(): void {
        Cache::forget($this->lockedSequenceKey);
    }

    public function getLockedSequenceNumber(): int {
        return Cache::get($this->lockedSequenceKey, 0);
    }

    public function tryAttempt(): bool {
        $result = $this->limiter->hit(($this->throttleTimerCalculator)($this->getLockedSequenceNumber()));

        if ($result === LimiterHitResult::HitAndLocked) {
            $this->increaseThrottleTime();
            return true;
        }

        return $result !== LimiterHitResult::WasLocked;
    }

    public function attempsRemaining(): int {
        return $this->limiter->attempsRemaining();
    }

    public function reset(): void {
        $this->limiter->reset();
        $this->resetThrottleTime();
    }

    public function lockedFor(): CarbonInterval | null {
        return $this->limiter->lockedFor();
    }
}
