<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\MissingStringValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class OpenTextVariable implements VariableInterface
{
    use GetName, GetTitle;
    /**
     * @phpstan-param non-empty-list<string> $dataPath
     * @param array<string, string> $titles
     */
    public function __construct(
        string $name,
        array $titles,
        private array $dataPath
    ) {
        $this->titles = $titles;

        $this->name = $name;
    }



    public function getValue(RecordInterface $record): StringValueInterface
    {
        $result = $record->getDataValue($this->dataPath);

        if ($result === null) {
            return new MissingStringValue('', true);
        }

        return new StringValue(is_array($result) ? print_r($result, true) : (string) $result);
    }

    public function getDisplayValue(RecordInterface $record, null|string $locale = null): StringValueInterface
    {
        return $this->getValue($record);
    }

    public function getMeasure(): string
    {
        return "nominal";
    }
}
