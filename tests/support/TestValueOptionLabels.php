<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\DataInterfaces\ValueInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use PHPUnit\Framework\Assert;

trait TestValueOptionLabels
{
    /**
     * @param \Closure $factory
     * @param array<string, string> $labels
     */
    private function assertValueOptionLabels(\Closure $factory, array $labels): void
    {
        $unknownLocale = random_bytes(14);
        $option = $factory($labels);
        Assert::assertInstanceOf(ValueOptionInterface::class, $option);

        if ($labels === []) {
            Assert::assertEquals((string) $option->getRawValue(), $option->getDisplayValue());
            Assert::assertEquals((string) $option->getRawValue(), $option->getDisplayValue($unknownLocale));
            return;
        }

        foreach ($labels as $locale => $label) {
            Assert::assertSame($label, $option->getDisplayValue($locale));
        }

        // Test missing locale.
        $unknownLocale = random_bytes(14);
        Assert::assertArrayNotHasKey($unknownLocale, $labels);
        if (isset($labels[ValueOptionInterface::DEFAULT_LOCALE])) {
            // Assert default locale is used if no locale is passed
            Assert::assertSame($labels[ValueOptionInterface::DEFAULT_LOCALE], $option->getDisplayValue());
            // And if locale is unknown
            Assert::assertSame($labels[ValueOptionInterface::DEFAULT_LOCALE], $option->getDisplayValue($unknownLocale));
        }

        Assert::assertContains($option->getDisplayValue(), $labels, "OptionValues should use any available locale when no locale is requested and no default was configured, labels: " . print_r($labels, true));
    }
}
