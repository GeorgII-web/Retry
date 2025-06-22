<?php

declare(strict_types=1);

namespace GeorgII\Example;

use Exception;
use GeorgII\Retry;

/**
 * @psalm-suppress UnusedClass
 *
 *                 @codeCoverageIgnoreStart
 */
final class RelatedExceptionExample
{
    public function relatedExceptionExample(): void
    {
        echo Retry::onRelatedExceptions(
            fn(): int => max(1, 1),
            [Exception::class],
        );
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
