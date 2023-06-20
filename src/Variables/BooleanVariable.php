<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueType;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\BooleanValueOption;
use Collecthor\SurveyjsParser\Values\NotNormalValue;

/**
 * @implements ClosedVariableInterface<bool>
 */
final class BooleanVariable implements ClosedVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    private readonly BooleanValueOption $yes;
    private readonly BooleanValueOption $no;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param array<string, string> $trueLabels
     * @param array<string, string> $falseLabels
     * @param array<string, mixed> $rawConfiguration
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private readonly string $name,
        private readonly array $titles,
        private readonly array $trueLabels,
        private readonly array $falseLabels,
        private readonly array $dataPath,
        private readonly array $rawConfiguration = []
    ) {
        $this->yes = new BooleanValueOption(true, $this->trueLabels);
        $this->no = new BooleanValueOption(false, $this->falseLabels);
    }

    /**
     * @return non-empty-list<ValueOptionInterface<bool>>
     */
    public function getValueOptions(): array
    {
        return [
            $this->yes,
            $this->no
        ];
    }

    public function getValue(RecordInterface $record): BooleanValueOption|NotNormalValueInterface
    {
        $dataValue = $record->getDataValue($this->dataPath);
        if (is_null($dataValue)) {
            return new NotNormalValue(ValueType::Missing);
        }
        if (!is_bool($dataValue)) {
            return new NotNormalValue(ValueType::Invalid);
        }
        return $dataValue ? $this->yes : $this->no;
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
