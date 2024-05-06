<?php

namespace App\Lib;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

class ThrottleTimerCalculator {
    public function __invoke(int $failedAttemps) {
        return pow(2, $failedAttemps + 1) * 60;
    }
}

class LoginThrottler {
    protected Limiter $limiter;
    protected string $key;
    protected string $failedAttempsKey;

    function __construct(
            string $key,
            int $maxAttempts = 5,
            public ThrottleTimerCalculator $throttleTimerCalculator = new ThrottleTimerCalculator) {
        $this->limiter = new Limiter(key: "login-throttler-limiter-$key", maxAttempts: $maxAttempts);
        $this->key = "login-throttler-$key";
        $this->failedAttempsKey = "{$this->key}-failed-batch-attemps";
    }

    protected function increaseThrottleTime(): void {
        $failedAttemps = $this->getFailedAttemps();
        $oldThrottleTime = $this->throttleTimerCalculator->__invoke($failedAttemps);
        $newThrottleTime = $this->throttleTimerCalculator->__invoke($failedAttemps + 1);

        Cache::put($this->failedAttempsKey, $failedAttemps + 1, $oldThrottleTime + $newThrottleTime);
    }

    protected function resetThrottleTime(): void {
        Cache::forget($this->failedAttempsKey);
    }

    public function getFailedAttemps(): int {
        return Cache::get($this->failedAttempsKey, 0);
    }

    public function tryAttempt(): bool {
        $result = $this->limiter->hit($this->throttleTimerCalculator->__invoke($this->getFailedAttemps()));

        if ($result === LimiterHitResult::HitAndLocked) {
            $this->increaseThrottleTime();
            return true;
        }

        return $result !== LimiterHitResult::WasLocked;
    }

    public function attempsRemaining(): int {
        return $this->limiter->attempsRemaining();
    }

    public function clearFailedAttemps(): void {
        $this->limiter->reset();
        $this->resetThrottleTime();
    }

    public function lockedFor(): CarbonInterval | null {
        return $this->limiter->lockedFor();
    }
}
