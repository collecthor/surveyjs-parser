<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\ValueSetInterface;

class ValueSet implements ValueSetInterface
{
    /** @param ValueOptionInterface[] $values */
    public function __construct(private array $values)
    {
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
