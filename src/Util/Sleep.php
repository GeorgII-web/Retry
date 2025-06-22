<?php

declare(strict_types=1);

namespace GeorgII\Util;

class Sleep
{
    /**
     * Sleep for the specified number of seconds using non-blocking usleep in a loop.
     *
     * @param float $seconds Number of seconds to wait. Can include fractions (e.g., 1.5 for 1.5 seconds).
     * @param float $step    Interval for each iteration (defaults to 0.1 seconds).
     */
    public static function delay(float $seconds, float $step = 0.1): void
    {
        if ($seconds > 0) {
            $remainingTime = $seconds;

            while ($remainingTime > 0) {
                $currentStep = min($remainingTime, $step); // Take the smaller of remaining time or step size
                usleep((int) round($currentStep * 1_000_000.00, 0));  // Sleep for the current step size in microseconds
                $remainingTime -= $currentStep;          // Decrease the remaining time
            }
        }
    }
}
