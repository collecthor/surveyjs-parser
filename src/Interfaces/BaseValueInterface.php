<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * This models the smallest piece of data.
 * A single answer with a multivalued variable or the whole answer in a single valued variable.
 */
interface BaseValueInterface
{
    /**
     * @return string|int|float|bool|\DateTimeInterface|array<mixed>|null
     */
    public function getValue(): string|int|float|bool|\DateTimeInterface|array|null;

    public function getDisplayValue(string|null $locale = null): string;
}
