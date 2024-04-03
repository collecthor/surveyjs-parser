<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;

final readonly class MultipleChoiceValue implements BaseValueInterface
{
    /**
     * @var list<ValueOptionInterface> $values
     */
    private array $values;

    public function __construct(
        ValueOptionInterface ...$values
    ) {
        $this->values = array_values($values);
    }


    /**
     * @return list<ValueOptionInterface>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    public function getIndex(int $index): ValueOptionInterface|null
    {
        return $this->values[$index] ?? null;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return implode(", ", array_map(static fn (ValueOptionInterface $option) => $option->getDisplayValue($locale), $this->values));
    }
}
