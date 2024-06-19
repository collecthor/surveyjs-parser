<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface TimestampVariableInterface extends VariableInterface
{
    public function getValue(RecordInterface $record): DateTimeValueInterface|SpecialValueInterface;

    /**
     * @return Measure::Scale
     */
    public function getMeasure(): Measure;
}
