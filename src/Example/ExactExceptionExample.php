<?php

declare(strict_types=1);

namespace GeorgII\Example;

use Exception;
use GeorgII\Retry;

/**
 * @psalm-suppress UnusedClass
 *
 * @codeCoverageIgnoreStart
 */
final class ExactExceptionExample
{
    public function exactExceptionExample(): void
    {
        echo Retry::onExactExceptions(
            fn(): int => max(1, 1),
            [Exception::class],
        );
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
