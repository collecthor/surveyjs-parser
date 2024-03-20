<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface StringVariableInterface extends VariableInterface
{
    public function getValue(RecordInterface $record): StringValueInterface|SpecialValueInterface;
}
