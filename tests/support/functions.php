<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use PHPUnit\Framework\Assert;

/**
 * @template T
 * @param class-string<T> $className
 * @param iterable<mixed> $haystack
 * @return void
 * @phpstan-assert iterable<T> $haystack
 */
function assertContainsOnlyInstancesOfFixed(string $className, iterable $haystack): void
{
    Assert::assertContainsOnlyInstancesOf($className, $haystack);
}
