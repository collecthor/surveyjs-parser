<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Interfaces\StringVariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;
use Collecthor\SurveyjsParser\Values\StringValue;

final readonly class OpenTextVariable implements StringVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;
    /**
     * @param array<string, string> $titles
     * @phpstan-param non-empty-list<string> $dataPath
     * @param array<string|int, mixed> $rawConfiguration
     */
    public function __construct(
        private string $name,
        private array $dataPath,
        private array $titles = [],
        private array $rawConfiguration = []
    ) {
    }

    public function getValue(RecordInterface $record): StringValueInterface|SpecialValueInterface
    {
        $result = $record->getDataValue($this->dataPath);
        if ($result === null) {
            return MissingValue::create();
        } elseif (is_scalar($result)) {
            return new StringValue($result);
        }

        return new InvalidValue($result);
    }
    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
