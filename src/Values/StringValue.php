<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\StringValueInterface;

class StringValue implements StringValueInterface
{
    public function __construct(private string $rawValue)
    {
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }
}
