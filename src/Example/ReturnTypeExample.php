<?php

declare(strict_types=1);

namespace GeorgII\Example;

use GeorgII\Retry;

/**
 * @psalm-suppress UnusedClass
 *
 * @codeCoverageIgnoreStart
 */
final class ReturnTypeExample
{
    public function returnPositiveIntExample(): void
    {
        $number = Retry::onAnyException(fn(): int => max(1, 1));
        $this->testPositiveInt($number);
    }

    public function returnNegativeIntExample(): void
    {
        $number = Retry::onAnyException(fn(): int => -1);
        /**
         * @psalm-suppress InvalidArgument Psalm will fail here
         */
        $this->testPositiveInt($number);
    }

    /**
     * @param positive-int $val
     */
    public function testPositiveInt(int $val): void
    {
        echo $val;
    }
}
/**
 * @codeCoverageIgnoreEnd
 */
