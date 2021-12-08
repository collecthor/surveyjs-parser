<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class SingleChoiceVariable implements ClosedVariableInterface
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

    public function getValue(RecordInterface $record): ValueOptionInterface|InvalidValue
    {
        // Match the value options.
        $rawValue = $record->getDataValue($this->dataPath);
        if (is_array($rawValue) || !isset($this->valueMap[(string) $rawValue])) {
            return new InvalidValue($rawValue);
        }

        return $this->valueMap[(string) $rawValue];
    }

    public function getDisplayValue(RecordInterface $record, ?string $locale = null): StringValueInterface
    {
        $value = $this->getValue($record);
        if (!$value instanceof StringValueInterface) {
            return new StringValue($value->getDisplayValue($locale));
        }
        return $value;
    }

    public function getMeasure(): string
    {
        return "nominal";
    }
}
