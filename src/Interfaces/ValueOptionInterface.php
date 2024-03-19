<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface ValueOptionInterface extends BaseValueInterface
{
    public const DEFAULT_LOCALE = 'default';

    /**
     * @return array<string, string> Display values for each available locale, indexed by locale
     */
    public function getDisplayValues(): array;

    public function getValue(): int|string|bool;
}
