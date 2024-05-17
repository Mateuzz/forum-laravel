<?php

namespace App\Lib;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

class ThrottleTimerCalculator {
    public function __invoke(int $lockedSequenceCount) {
        return pow(2, $lockedSequenceCount + 1) * 60;
    }
}

class LoginThrottler {
    public ThrottleTimerCalculator $throttleTimerCalculator = new ThrottleTimerCalculator;
    protected Limiter $limiter;
    protected string $key;
    private string $lockedSequenceCountKey;

    function __construct(string $key, int $maxAttempts = 5) {
        $this->limiter = new Limiter(key: "login-throttler-limiter-$key", maxAttempts: $maxAttempts);
        $this->key = "login-throttler-$key";
        $this->lockedSequenceCountKey = "{$this->key}-locked-sequence";
    }

    private function increaseThrottleTime(): void {
        $lockedSequenceCount = $this->getLockedSequenceCount();
        $oldThrottleTime = ($this->throttleTimerCalculator)($lockedSequenceCount);
        $newThrottleTime = ($this->throttleTimerCalculator)($lockedSequenceCount + 1);

        // the increased throttle time will reset if no attempt is made during the same period
        Cache::put($this->lockedSequenceCountKey, $lockedSequenceCount + 1, $oldThrottleTime + $newThrottleTime);
    }

    private function resetThrottleTime(): void {
        Cache::forget($this->lockedSequenceCountKey);
    }

    public function getLockedSequenceCount(): int {
        return Cache::get($this->lockedSequenceCountKey, 0);
    }

    public function tryAttempt(): bool {
        $result = $this->limiter->hit(($this->throttleTimerCalculator)($this->getLockedSequenceCount()));

        if ($result === LimiterHitResult::HitAndLocked) {
            return $this->increaseThrottleTime();
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
