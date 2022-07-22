<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueInterface;

class ArrayValue implements ValueInterface
{
    /**
     * @param array<mixed> $rawValue
     */
    public function __construct(private readonly array $rawValue)
    {
    }

    /**
     * @return array<mixed>
     */
    public function getRawValue(): array
    {
        return $this->rawValue;
    }
}
