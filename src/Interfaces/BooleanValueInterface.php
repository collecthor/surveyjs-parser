<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * This models the smallest piece of data.
 * A single answer with a multivalued variable or the whole answer in a single valued variable.
 */
interface BooleanValueInterface extends BaseValueInterface, ValueOptionInterface
{
    public function getValue(): bool;

    public function getDisplayValue(string|null $locale = null): string;
}
