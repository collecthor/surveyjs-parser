<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * A closed variable is a variable with a finite number of valid values
 * @template T of string|int|float|bool
 */
interface ClosedVariableInterface extends VariableInterface
{
    /**
     * @return non-empty-list<ValueOptionInterface<T>> A list of value options
     */
    public function getValueOptions(): array;

    /**
     * @param RecordInterface $record
     * @return NotNormalValueInterface|ValueSetInterface<T>|ValueOptionInterface<T>
     */
    public function getValue(RecordInterface $record): RawValueInterface|ValueSetInterface|ValueOptionInterface;
}
