<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\InvalidValueInterface;
use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\NumericValueInterface;
use Collecthor\DataInterfaces\NumericVariableInterface;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\FloatValue;
use Collecthor\SurveyjsParser\Values\IntegerValue;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\MissingIntegerValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class NumericVariable implements NumericVariableInterface, JavascriptVariableInterface
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
            return new FloatValue($result);
        }
        if (is_int($result)) {
            return new IntegerValue($result);
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
    public function getMeasure(): Measure
    {
        return Measure::Ordinal;
    }

    public function getJavascriptRepresentation(): string
    {
        $config = json_encode([
            'titles' => $this->titles,
            'dataPath' => $this->dataPath,
            'measure' => $this->getMeasure()->value,

        ], JSON_THROW_ON_ERROR);
        return <<<JS
            (() => {
                const config = $config;
                const getDataValue = (record, path) => {
                    const length = path.length;
                    let subject = record;
                    for(let i = 0; i < length; i ++) {
                        subject = record[path[i]] ?? null;
                    }
                    return subject;                    
                }
                
                return {
                    getTitle(locale = null) => config.titles[locale ?? 'default'] ?? Object.values(config.titles)[0],
                    getMeasure() => config.measure,
                    getValue(record) => {
                        const raw = getDataValue(record, path)
                        if (raw === null) {
                            return null;
                        }
                        if (typeof raw !== 'number') {
                            return 'INVALID_VALUE';
                        }
                        return raw;                       
                    },
                    getDisplayValue(record) => {
                        const raw = getDataValue(record, path)
                        if (raw === null) {
                            return null;
                        }
                        if (typeof raw !== 'number') {
                            return 'INVALID_VALUE';
                        }
                        return raw; 
                    }
                    
                }
    
            })()

JS;
    }
}
