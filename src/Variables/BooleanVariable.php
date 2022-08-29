<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\BooleanValue;
use Collecthor\SurveyjsParser\Values\BooleanValueOption;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingBooleanValue;
use Collecthor\SurveyjsParser\Values\StringValue;
use InvalidArgumentException;

final class BooleanVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;
    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param array<string,array<string,string>> $booleanNames
     * @param array<string, mixed> $rawConfiguration
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private string $name,
        private array $titles,
        readonly array $booleanNames,
        private readonly array $dataPath,
        private array $rawConfiguration = []
    ) {
        if (!isset($booleanNames['true']) || !isset($booleanNames['false'])) {
            throw new InvalidArgumentException("Titles for BooleanVariable must contain values for true and false");
        }
    }

    public function getValueOptions(): array
    {
        return [
            new BooleanValueOption(true, $this->booleanNames['true']),
            new BooleanValueOption(false, $this->booleanNames['false']),
        ];
    }

    public function getValue(RecordInterface $record): BooleanValue | MissingBooleanValue | InvalidValue
    {
        $dataValue = $record->getDataValue($this->dataPath);
        if (is_null($dataValue)) {
            return new MissingBooleanValue();
        }
        if (!is_bool($dataValue)) {
            return new InvalidValue($dataValue);
        }
        return new BooleanValue($dataValue);
    }

    public function getDisplayValue(RecordInterface $record, ?string $locale = 'default'): StringValueInterface
    {
        $result = $this->getValue($record);
        if ($result instanceof InvalidValue || $result instanceof MissingBooleanValue) {
            return new StringValue((string) $result->getRawValue());
        } else {
            if ($result->getRawValue()) {
                return new StringValue($this->booleanNames['true'][$locale]);
            } else {
                return new StringValue($this->booleanNames['false'][$locale]);
            }
        }
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
