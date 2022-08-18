<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\ValueInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\ValueSetInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Values\ValueSet;
use Exception;

class MultipleChoiceVariable implements ClosedVariableInterface
{
    use GetName, GetTitle;

    /**
     * @var array<string, ValueOptionInterface>
     */
    private array $valueMap = [];

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @phpstan-param non-empty-list<ValueOptionInterface> $valueOptions
     */
    public function __construct(
        string $name,
        array $titles,
        array $valueOptions,
        /**
         * @phpstan-var non-empty-list<string>
         */
        private array $dataPath
    ) {
        $this->name = $name;

        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getRawValue()] = $valueOption;
        }

        $this->titles = $titles;
    }

    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    public function getValue(RecordInterface $record): ValueInterface|ValueSetInterface
    {
        $rawValues = $record->getDataValue($this->dataPath);
        if (!is_array($rawValues)) {
            return new InvalidValue($rawValues);
        }

        $values = [];

        foreach ($rawValues as $value) {
            /** @var string $value */
            if (isset($this->valueMap[(string) $value])) {
                $values[] = $this->valueMap[(string) $value];
            } else {
                return new InvalidValue($rawValues);
            }
        }
        return new ValueSet($values);
    }

    public function getDisplayValue(RecordInterface $record, ?string $locale = null): StringValueInterface
    {
        /** @var ValueSetInterface $valueSet */
        $valueSet = $this->getValue($record);
        $values = $valueSet->getValues();

        $displayValues = array_map(fn (ValueOptionInterface $val) => $val->getDisplayValue($locale), $values);
        return new StringValue(implode(", ", $displayValues));
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
