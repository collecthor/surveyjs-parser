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
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use Collecthor\SurveyjsParser\Values\SystemMissingValue;
use Collecthor\SurveyjsParser\Values\ValueSet;
use InvalidArgumentException;

class MultipleChoiceVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var non-empty-array<string, ValueOptionInterface>
     */
    private array $valueMap;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param non-empty-list<ValueOptionInterface> $valueOptions
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
        if (count($valueOptions) === 0) {
            throw new InvalidArgumentException('ValueOptions must not be empty');
        }
        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getRawValue()] = $valueOption;
        }
    }

    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    public function getValue(RecordInterface $record): ValueInterface|ValueSetInterface
    {
        $rawValues = $record->getDataValue($this->dataPath);
        if ($rawValues === null) {
            return new SystemMissingValue();
        } elseif (!is_array($rawValues)) {
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
        return new ValueSet(...$values);
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
