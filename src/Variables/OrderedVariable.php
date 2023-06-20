<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueSetInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\NotNormalValue;
use Collecthor\SurveyjsParser\Values\ValueSet;
use ValueError;
use function count;

/**
 * @template T of string|int|float|bool
 * @implements ClosedVariableInterface<T>
 */
final class OrderedVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var non-empty-array<string, ValueOptionInterface<T>>
     */
    private array $valueMap;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param list<ValueOptionInterface<T>> $valueOptions
     * @param array<string, mixed> $rawConfiguration
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private readonly string $name,
        private readonly array $titles,
        array $valueOptions,
        private readonly array $dataPath,
        private readonly array $rawConfiguration = []
    ) {
        $this->checkValueOptions($valueOptions);
        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getValue()] = $valueOption;
        }
    }

    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    /**
     * @param RecordInterface $record
     * @return NotNormalValueInterface|ValueSetInterface<T>
     */
    public function getValue(RecordInterface $record): NotNormalValueInterface | ValueSetInterface
    {
        $rawValues = $record->getDataValue($this->dataPath);
        if ($rawValues === null) {
            return NotNormalValue::missing();
        } elseif (!is_array($rawValues)) {
            return NotNormalValue::invalid($rawValues);
        }

        $values = [];

        foreach ($rawValues as $value) {
            /** @var string $value */
            if (is_scalar($value) && isset($this->valueMap[(string) $value])) {
                $values[] = $this->valueMap[(string) $value];
            } else {
                return NotNormalValue::invalid($rawValues);
            }
        }
        return new ValueSet(...$values);
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }

    /**
     * @template X
     * @phpstan-assert non-empty-list<X> $valueOptions
     * @param list<X> $valueOptions
     */
    private function checkValueOptions(array $valueOptions): void
    {
        if (count($valueOptions) < 1) {
            throw new ValueError("Valueoptions must not be empty");
        }
    }
}
