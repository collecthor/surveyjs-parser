<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * @template-covariant T of string|int|float|bool
 */
interface ValueOptionInterface extends RawValueInterface
{
    public const DEFAULT_LOCALE = 'default';

    /**
     * @return T
     */
    public function getValue(): string|int|float|bool;


    /**
     * @return T
     */
    public function getRawValue(): string|int|float|bool;

    /**
     * @return array<string, string> Display values for each available locale, indexed by locale
     */
    public function getDisplayValues(): array;

    public function isNone(): bool;

    public function isOther(): bool;
}
