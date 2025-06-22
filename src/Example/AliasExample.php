<?php

declare(strict_types=1);

namespace GeorgII\Example;

use Closure;
use Exception;
use GeorgII\Event\RetryEvent;
use GeorgII\Retry;

/**
 * @psalm-suppress UnusedClass
 *
 * @codeCoverageIgnoreStart
 */
final class AliasExample
{
    /**
     * @template R
     *
     * @param Closure(RetryEvent): R $callback
     *
     * @return R
     */
    public static function aliasCreateExample(Closure $callback): mixed
    {
        return Retry::onRelatedExceptions(
            $callback,
            [Exception::class]
        );
    }

    public function aliasUsageExample(): void
    {
        echo self::aliasCreateExample(fn() => 1);
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
