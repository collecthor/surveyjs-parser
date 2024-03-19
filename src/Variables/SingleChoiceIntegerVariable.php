<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\IntegerValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;

final readonly class SingleChoiceIntegerVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var non-empty-array<string|int, ValueOptionInterface|SpecialValueOptionInterface>
     */
    private array $valueMap;

    /**
     * @param array<string, string> $titles
     * @param non-empty-list<IntegerValueOptionInterface> $valueOptions
     * @param non-empty-list<string> $dataPath
     * @param array<string, mixed> $rawConfiguration
     */
    public function __construct(
        private string $name,
        private array $titles,
        array $valueOptions,
        private array $dataPath,
        private array $rawConfiguration = [],
        private Measure $measure = Measure::Nominal
    ) {
        $valueMap = [];
        foreach ($valueOptions as $valueOption) {
            $valueMap[$valueOption->getValue()] = $valueOption;
        }
        $this->valueMap = $valueMap;
    }

    public function getValue(RecordInterface $record): ValueOptionInterface|SpecialValueInterface
    {
        $rawValue = $record->getDataValue($this->dataPath);
        if (is_array($rawValue)) {
            return new InvalidValue($rawValue);
        } elseif ($rawValue === null) {
            return MissingValue::create();
        }

        return $this->valueMap[$rawValue];
    }

    public function getMeasure(): Measure
    {
        return $this->measure;
    }

    public function getOptions(): array
    {
        return array_values($this->valueMap);
    }
}
