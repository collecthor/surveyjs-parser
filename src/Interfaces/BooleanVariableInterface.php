<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

/**
 * @internal
 */
interface BooleanVariableInterface extends VariableInterface, ClosedVariableInterface
{
    public function getValue(RecordInterface $record): BooleanValueInterface|SpecialValueInterface;
}
