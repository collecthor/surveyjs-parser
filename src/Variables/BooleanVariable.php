<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\BooleanVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\BooleanValueOption;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingValue;

final readonly class BooleanVariable implements BooleanVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    private BooleanValueOption $yes;
    private BooleanValueOption $no;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param array<string, string> $trueLabels
     * @param array<string, string> $falseLabels
     * @param array<mixed> $rawConfiguration
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private string $name,
        private array $dataPath,
        private array $titles = [],
        private array $trueLabels = [],
        private array $falseLabels = [],
        private array $rawConfiguration = [],
        bool|string $trueValue = true,
        bool|string $falseValue = false,
    ) {
        $this->yes = new BooleanValueOption($trueValue, true, $this->trueLabels);
        $this->no = new BooleanValueOption($falseValue, false, $this->falseLabels);
    }

    public function getOptions(): array
    {
        return [
            $this->yes,
            $this->no
        ];
    }

    public function getValue(RecordInterface $record): BooleanValueOption|SpecialValueInterface
    {
        $dataValue = $record->getDataValue($this->dataPath);
        if (is_null($dataValue)) {
            return MissingValue::create();
        }
        if ($dataValue === $this->yes->getRawValue()) {
            return $this->yes;
        } elseif ($dataValue === $this->no->getRawValue()) {
            return $this->no;
        }

        return new InvalidValue($dataValue);
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }

    public function getNumberOfOptions(): int
    {
        return 2;
    }
}
