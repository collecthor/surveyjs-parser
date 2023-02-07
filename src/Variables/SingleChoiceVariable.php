<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\InvalidValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class SingleChoiceVariable implements ClosedVariableInterface, JavascriptVariableInterface
{
    use GetName, GetTitle, GetRawConfiguration;

    /**
     * We can say this is non-empty, since valueoptions is non-empty, and this is a direct mapping from valueoptions
     * @var non-empty-array<string, ValueOptionInterface>
     */
    private array $valueMap;

    /**
     * @param string $name
     * @param array<string, string> $titles
     * @param array<string, mixed> $rawConfiguration
     * @param non-empty-list<ValueOptionInterface> $valueOptions
     * @param non-empty-list<string> $dataPath
     */
    public function __construct(
        private readonly string $name,
        private readonly array $titles,
        array $valueOptions,
        private readonly array $dataPath,
        private readonly array $rawConfiguration = []
    ) {
        foreach ($valueOptions as $valueOption) {
            $this->valueMap[(string) $valueOption->getRawValue()] = $valueOption;
        }
    }

    public function getValueOptions(): array
    {
        return array_values($this->valueMap);
    }

    public function getValue(RecordInterface $record): ValueOptionInterface|InvalidValue
    {
        // Match the value options.
        $rawValue = $record->getDataValue($this->dataPath);
        if (is_scalar($rawValue) && isset($this->valueMap[(string) $rawValue])) {
            return $this->valueMap[(string) $rawValue];
        }

        return new InvalidValue($rawValue);
    }

    public function getDisplayValue(RecordInterface $record, ?string $locale = null): StringValueInterface
    {
        $value = $this->getValue($record);
        if (!$value instanceof StringValueInterface) {
            return new StringValue($value->getDisplayValue($locale));
        }
        return $value;
    }

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
    }

    public function getJavascriptRepresentation(): string
    {
        $valueMap = [];
        foreach ($this->valueMap as $option) {
            $valueMap[(string) $option->getRawValue()] = [
                'raw' => $option->getRawValue(),
                'displayValues' => $option->getDisplayValues()
            ];
        }
        $config = json_encode([
            'titles' => $this->titles,
            'dataPath' => $this->dataPath,
            'valueMap' => $valueMap,
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
                    getValue(record) => getDataValue(record, path),
                    getDisplayValue(record) => getDataValue(record, path)                    
                }
    
            })()

JS;
    }
}
