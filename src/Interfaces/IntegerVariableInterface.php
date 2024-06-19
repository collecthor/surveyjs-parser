<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface IntegerVariableInterface extends VariableInterface
{
    public function getValue(RecordInterface $record): IntegerValueInterface|SpecialValueInterface;

    /**
     * @return Measure::Ordinal|Measure::Scale
     */
    public function getMeasure(): Measure;
}
