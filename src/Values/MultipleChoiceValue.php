<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;

final readonly class MultipleChoiceValue implements BaseValueInterface
{
    /**
     * @param list<ValueOptionInterface> $values
     */
    public function __construct(
        private array $values
    ) {
    }


    /**
     * @return list<ValueOptionInterface>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return implode(", ", array_map(static fn (ValueOptionInterface $option) => $option->getDisplayValue($locale), $this->values));
    }
}
