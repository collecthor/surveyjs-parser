<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueInterface;

class BooleanValue implements ValueInterface
{
    public function __construct(private readonly bool $rawValue)
    {
    }
    public function getRawValue(): bool
    {
        return $this->rawValue;
    }
}
