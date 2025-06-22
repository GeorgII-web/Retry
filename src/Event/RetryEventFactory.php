<?php

declare(strict_types=1);

namespace GeorgII\Event;

use GeorgII\Retry\RetryCore;
use Throwable;

readonly class RetryEventFactory
{
    public function __construct(
        private RetryCore $retry,
    ) {
    }

    /**
     * @param non-empty-string $name
     * @param positive-int     $attempt
     */
    public function create(string $name, int $attempt, ?Throwable $exception): RetryEvent
    {
        return new RetryEvent(
            $name,
            $attempt,
            $this->retry->getRetryCount(),
            $this->retry->getRetryDelay(max(1, $attempt)),
            $exception
        );
    }
}
