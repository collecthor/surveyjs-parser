<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\Interfaces\NotNormalValueInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\NotNormalValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class OpenTextVariable implements VariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;
    /**
     * @param array<string, string> $titles
     * @param array<string, mixed> $rawConfiguration
     * @phpstan-param non-empty-list<string> $dataPath
     */
    public function __construct(
        string $name,
        array $titles,
        private readonly array $dataPath,
        array $rawConfiguration = []
    ) {
        $this->name = $name;
        $this->titles = $titles;
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * @param RecordInterface $record
     * @return StringValueInterface|NotNormalValueInterface
     */
    public function getValue(RecordInterface $record): StringValueInterface|NotNormalValueInterface
    {
        $result = $record->getDataValue($this->dataPath);

        if ($result === null) {
            NotNormalValue::missing();
        } elseif (is_scalar($result)) {
            return StringValue::fromRawValue($result);
        }

        return NotNormalValue::invalid($result);
    }
    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }
}
