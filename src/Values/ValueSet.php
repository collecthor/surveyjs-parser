<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueSetInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use function iter\map;

/**
 * @template T of string|float|int|bool
 * @implements ValueSetInterface<T>
 */
class ValueSet implements ValueSetInterface
{
    /** @var array<ValueOptionInterface<T>> $values */
    private array $values = [];

    /**
     * @param ValueOptionInterface<T> ...$values
     */
    public function __construct(ValueOptionInterface ...$values)
    {
        $this->values = $values;
    }

    /**
     * @return list<ValueOptionInterface<T>>
     */
    public function getRawValue(): array
    {
        return $this->values;
    }

    /**
     * @return list<ValueOptionInterface<T>>
     */
    public function getValue(): array
    {
        return $this->values;
    }

    public function getType(): ValueType
    {
        return ValueType::Normal;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return \iter\join(", ", map(fn (ValueOptionInterface $option) => $option->getDisplayValue($locale), $this->values));
    }
}
