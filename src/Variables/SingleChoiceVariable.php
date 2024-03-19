<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;

final readonly class SingleChoiceVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var array<int|string, ValueOptionInterface>
     */
    private array $valueMap;

    /**
     * @param array<string, string> $titles
     * @param list<ValueOptionInterface> $options
     * @param non-empty-list<string> $dataPath
     * @param array<mixed> $rawConfiguration
     */
    public function __construct(
        private string $name,
        array $options,
        private array $dataPath,
        private array $rawConfiguration = [],
        private array $titles = [],
        private Measure $measure = Measure::Nominal
    ) {
        $valueMap = [];
        foreach ($options as $valueOption) {
            $valueMap[$valueOption->getValue()] = $valueOption;
        }
        $this->valueMap = $valueMap;
    }

    public function getValue(RecordInterface $record): ValueOptionInterface|SpecialValueInterface
    {
        // Match the value options.
        $rawValue = $record->getDataValue($this->dataPath);
        if ($rawValue === null) {
            return MissingValue::create();
        } elseif (is_array($rawValue) || is_float($rawValue)) {
            return new InvalidValue($rawValue);
        }
        return $this->valueMap[$rawValue] ?? new InvalidValue($rawValue);
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
