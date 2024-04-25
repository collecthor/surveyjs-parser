<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class ValueNode extends Node
{
    public function __construct(public readonly string|int|float $value)
    {
    }


    public function print(): string
    {
        return "Value(" . json_encode($this->value) . ")";
    }
}
