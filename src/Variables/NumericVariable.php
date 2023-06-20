<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\NumericValueInterface;
use Collecthor\SurveyjsParser\Interfaces\NumericVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\RawValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Values\NotNormalValue;

class NumericVariable implements VariableInterface
{
    use GetTitle, GetName, GetRawConfiguration;

    /**
     * @param array<string, string> $titles
     * @param array<string, mixed> $rawConfiguration
     * @phpstan-param non-empty-list<string> $dataPath
     */
    public function __construct(
        string $name,
        array $titles,
        private readonly array $dataPath,
        array $rawConfiguration = [],
    ) {
        $this->titles = $titles;
        $this->name = $name;
        $this->rawConfiguration = $rawConfiguration;
    }

    public function getValue(RecordInterface $record): FloatValue|IntegerValue|NotNormalValueInterface
    {
        $result = $record->getDataValue($this->dataPath);
        if ($result === null) {
            return NotNormalValue::missing();
        }

        if (is_float($result)) {
            return new FloatValue($result);
        }
        if (is_int($result)) {
            return new IntegerValue($result);
        }

        return NotNormalValue::invalid($result);
    }

    /**
     * Numerical variables are always ordered, they might even be scalar, but that depends on context.
     */
    public function getMeasure(): Measure
    {
        return Measure::Ordinal;
    }
}
