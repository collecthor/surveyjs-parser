<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\MultipleChoiceVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;
use Collecthor\SurveyjsParser\Values\MultipleChoiceValue;

final readonly class MultipleChoiceVariable implements MultipleChoiceVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var array<int|string, ValueOptionInterface>
     */
    private array $valueMap;

    /**
     * @param array<string, string> $titles
     * @param list<ValueOptionInterface> $options
     * @param non-empty-list<string> $dataPath
     * @param array<mixed> $rawConfiguration
     */
    public function __construct(
        private string $name,
        private array $dataPath,
        array $options,
        private array $titles = [],
        private array $rawConfiguration = [],
        private bool $ordered = false,
    ) {
        $valueMap = [];
        foreach ($options as $valueOption) {
            $valueMap[$valueOption->getValue()] = $valueOption;
        }
        $this->valueMap = $valueMap;
    }

    public function getValue(RecordInterface $record): SpecialValueInterface|MultipleChoiceValue
    {
        $rawValues = $record->getDataValue($this->dataPath);

        if (is_array($rawValues)) {
            $result = [];
            foreach ($rawValues as $value) {
                if (is_scalar($value) && isset($this->valueMap[$value])) {
                    $result[] = $this->valueMap[$value];
                } else {
                    return new InvalidValue($rawValues);
                }
            }
            return new MultipleChoiceValue($result);
        }

        if ($rawValues === null) {
            return MissingValue::create();
        }

        return new InvalidValue($rawValues);
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }

    public function getOptions(): array
    {
        return array_values($this->valueMap);
    }

    public function isOrdered(): bool
    {
        return $this->ordered;
    }
}
