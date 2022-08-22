<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\Measure;
use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StringValueInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\Traits\GetName;
use Collecthor\SurveyjsParser\Traits\GetRawConfiguration;
use Collecthor\SurveyjsParser\Traits\GetTitle;
use Collecthor\SurveyjsParser\Values\MissingStringValue;
use Collecthor\SurveyjsParser\Values\StringValue;

class OpenTextVariable implements VariableInterface, JavascriptVariableInterface
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

    public function getMeasure(): Measure
    {
        return Measure::Nominal;
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
                    getValue(record) => getDataValue(record, path),
                    getDisplayValue(record) => getDataValue(record, path)                    
                }
    
            })()

JS;
    }
}
