<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class UnescapedStringNode extends Node
{
    public function __construct(public string $value)
    {
    }


    public function print(): string
    {
        return "UnescapedStringValue(" . json_encode($this->value) . ")";
    }
}
