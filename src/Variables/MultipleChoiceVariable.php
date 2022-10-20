<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\ValueSetInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingStringValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\SystemMissingValue;
use Collecthor\SurveyjsParser\Values\ValueSet;

class MultipleChoiceVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * @var array<string, ValueOptionInterface>
     */
    private array $valueMap = [];

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param non-empty-list<ValueOptionInterface> $valueOptions
     * @param array<string, mixed> $rawConfiguration
     */
    public function __construct(
        string $name,
        array $titles,
        array $valueOptions,
        /**
         * @phpstan-var non-empty-list<string>
         */
        private array $dataPath,
        array $rawConfiguration = []
    ) {
        $this->name = $name;

        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getRawValue()] = $valueOption;
        }

        $this->titles = $titles;
        $this->rawConfiguration = $rawConfiguration;
    }

    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    public function getValue(RecordInterface $record): SystemMissingValue|InvalidValue|ValueSetInterface
    {
        $rawValues = $record->getDataValue($this->dataPath);
        if ($rawValues === null) {
            return new SystemMissingValue();
        } elseif (!is_array($rawValues)) {
            return new InvalidValue($rawValues);
        }

        $values = [];

        foreach ($rawValues as $value) {
            if (is_scalar($value) && isset($this->valueMap[(string) $value])) {
                $values[] = $this->valueMap[(string) $value];
            } else {
                return new InvalidValue($rawValues);
            }
        }
        return new ValueSet(...$values);
    }

    public function getDisplayValue(RecordInterface $record, ?string $locale = null): StringValueInterface
    {
        $valueSet = $this->getValue($record);
        if (!$valueSet instanceof ValueSetInterface) {
            return new MissingStringValue((string) $valueSet->getRawValue(), $valueSet->isSystemMissing());
        }
        $values = $valueSet->getValues();

        $displayValues = array_map(fn (ValueOptionInterface $val) => $val->getDisplayValue($locale), $values);
        return new StringValue(implode(", ", $displayValues));
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
