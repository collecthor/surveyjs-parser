<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\MissingValueInterface;
use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\NumericVariableInterface;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingIntegerValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class NumericVariable implements NumericVariableInterface
{
    use GetTitle, GetName;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @phpstan-param non-empty-list<string> $dataPath
     */
    public function __construct(
        string $name,
        array $titles,
        private array $dataPath
    ) {
        $this->titles = $titles;
        $this->name = $name;
    }

    public function getValue(RecordInterface $record): NumericValueInterface|InvalidValueInterface
    {
        $result = $record->getDataValue($this->dataPath);
        if ($result === null) {
            return new MissingIntegerValue(PHP_INT_MIN, true);
        }

        if (is_float($result)) {
            return new FloatValue((float) $result);
        }
        if (is_int($result)) {
            return new IntegerValue((int) $result);
        }

        return new InvalidValue($result);
    }

    public function getDisplayValue(
        RecordInterface $record,
        ?string $locale = null
    ): StringValueInterface {
        $result = $this->getValue($record);
        return new StringValue((string) $result->getRawValue());
    }

    /**
     * Numerical variables are always ordered, they might even be scalar, but that depends on context.
     */
    public function getMeasure(): string
    {
        return self::MEASURE_ORDINAL;
    }
}
