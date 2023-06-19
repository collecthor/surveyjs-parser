<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\JavascriptVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\NotNormalValue;

/**
 * @template T of string|int|float|bool
 * @implements ClosedVariableInterface<T>
 */
class SingleChoiceVariable implements ClosedVariableInterface
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
     * @param non-empty-list<ValueOptionInterface<T>> $valueOptions
     * @param non-empty-list<string> $dataPath
     * @param array<string, mixed> $rawConfiguration
     */
    public function __construct(
        private readonly string $name,
        private readonly array $titles,
        array $valueOptions,
        private readonly array $dataPath,
        private readonly array $rawConfiguration = []
    ) {
        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getValue()] = $valueOption;
        }
    }

    /**
     * @return non-empty-list<ValueOptionInterface<T>>
     */
    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    /**
     * @param RecordInterface $record
     * @return ValueOptionInterface<T>|NotNormalValueInterface
     */
    public function getValue(RecordInterface $record): ValueOptionInterface|NotNormalValueInterface
    {
        // Match the value options.
        $rawValue = $record->getDataValue($this->dataPath);
        if (is_scalar($rawValue) && isset($this->valueMap[(string) $rawValue])) {
            return $this->valueMap[(string) $rawValue];
        }

        return NotNormalValue::invalid($rawValue);
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
