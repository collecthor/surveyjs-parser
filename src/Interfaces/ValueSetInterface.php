<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * Models a value that contains multiple ValueOptions.
 * This is used in multiple response variables.
 * @template T of string|float|int|bool
 */
interface ValueSetInterface extends RawValueInterface
{
    /**
     * @return list<ValueOptionInterface<T>> A list of value options
     */
    public function getValue(): array;
}
