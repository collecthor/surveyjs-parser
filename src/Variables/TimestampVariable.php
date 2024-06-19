<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\DateTimeValueInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\TimestampVariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\DateTimeValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;

final readonly class TimestampVariable implements TimestampVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * @param array<string, string> $titles
     * @param array<mixed> $rawConfiguration
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private string $name,
        private array $dataPath,
        private array $titles = [],
        private array $rawConfiguration = [],
        private string $format = 'Y-m-d H:i:s'
    ) {
    }

    public function getValue(RecordInterface $record): DateTimeValueInterface|SpecialValueInterface
    {
        $dataValue = $record->getDataValue($this->dataPath);
        return match (true) {
            is_null($dataValue) => MissingValue::create(),
            $dataValue instanceof \DateTimeInterface => new DateTimeValue($dataValue, $this->format),
            is_int($dataValue), is_float($dataValue) => new DateTimeValue(new \DateTimeImmutable("@$dataValue"), $this->format),
            is_string($dataValue) => new DateTimeValue(new \DateTimeImmutable($dataValue), $this->format),
            default => new InvalidValue($dataValue)
        };
    }

    public function getMeasure(): Measure
    {
        return Measure::Scale;
    }
}
