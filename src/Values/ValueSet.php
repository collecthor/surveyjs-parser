<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\ValueSetInterface;

class ValueSet implements ValueSetInterface
{
    /** @var array<ValueOptionInterface> $values */
    private array $values = [];
    
    public function __construct(ValueOptionInterface ...$values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
