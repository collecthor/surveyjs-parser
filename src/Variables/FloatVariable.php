<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;

final readonly class FloatVariable implements VariableInterface
{
    use GetTitle, GetName, GetRawConfiguration;

    /**
     * @param array<string, string> $titles
     * @param array<string|int, mixed> $rawConfiguration
     * @phpstan-param non-empty-list<string> $dataPath
     */
    public function __construct(
        private string $name,
        private array $titles,
        private array $dataPath,
        private array $rawConfiguration = [],
    ) {
    }

    public function getValue(RecordInterface $record): FloatValue|IntegerValue|SpecialValueInterface
    {
        $result = $record->getDataValue($this->dataPath);
        if ($result === null) {
            return MissingValue::create();
        }

        if (is_float($result)) {
            return new FloatValue($result);
        }
        if (is_int($result)) {
            return IntegerValue::create($result);
        }

        return new InvalidValue($result);
    }

    /**
     * Numerical variables are always ordered, they might even be scalar, but that depends on context.
     */
    public function getMeasure(): Measure
    {
        return Measure::Ordinal;
    }
}
