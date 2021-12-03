<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;

class SurveyParser
{
    public const UNKNOWN_ELEMENT_TYPE = '__unknown__';

    /**
     * @var array<string, ElementParserInterface>
     */
    private array $parsers = [];

    private ElementParserInterface $recursiveParser;

    public function __construct()
    {
        // Recursive parser.
        $this->recursiveParser = new CallbackElementParser(
        /**
         * @param non-empty-array<string, mixed> $questionConfig
         */

        function (
            ElementParserInterface $parent,
            SurveyConfiguration $surveyConfiguration,
            array $questionConfig,
            array $dataPrefix = []
        ) {
            yield from $this->parseElement($questionConfig, $surveyConfiguration, $dataPrefix);
        }
        );

        // Configure default built-in parsers.
        $this->parsers['text'] = new TextQuestionParser();
        $this->parsers['comment'] = new TextQuestionParser();
        $this->parsers['expression'] = new TextQuestionParser();
        $this->parsers['panel'] = new PanelParser();

        $this->parsers['html'] = new DummyParser();
        $this->parsers['image'] = new DummyParser();
        $this->parsers[self::UNKNOWN_ELEMENT_TYPE] = new DummyParser();
    }


    private function getParser(string $type): ElementParserInterface
    {
        return $this->parsers[$type] ?? $this->parsers[self::UNKNOWN_ELEMENT_TYPE];
    }

    /**
     * @param array<string, mixed> $config
     * @param SurveyConfiguration $surveyConfiguration
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface>
     */
    private function parseElement(array $config, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if (!isset($config['type'], $this->parsers[$config['type']])) {
            $config['type'] = self::UNKNOWN_ELEMENT_TYPE;
        }
        /**
         * Because of the IF clause above, the array is now non-empty
         * @var non-empty-array<string, mixed> $config
         */

        $type = $config['type'];
        if (!is_string($type)) {
            throw new \InvalidArgumentException("Element type must be a strong, got: " . print_r($config, true));
        }
        yield from $this->getParser($type)->parse($this->recursiveParser, $config, $surveyConfiguration, $dataPrefix);
    }

    /**
     * @param array<string, non-empty-array<string, mixed>> $structure
     * @param SurveyConfiguration $surveyConfiguration
     * @return iterable<VariableInterface>
     */
    private function parsePage(array $structure, SurveyConfiguration $surveyConfiguration): iterable
    {
        foreach ($structure['elements'] as $element) {
            if (!is_array($element)) {
                throw new \InvalidArgumentException("Element must be an array, got: " . print_r([$element], true));
            }
            yield from $this->parseElement($element, $surveyConfiguration);
        }
    }

    /**
     * @param array<string, mixed> $structure
     */
    public function parseSurveyStructure(array $structure): VariableSetInterface
    {
        $variables = [];

        /**
         * Get some global settings from the survey structure. Note surveyJS incorrectly calls this a prefix
         * https://surveyjs.io/Documentation/Library?id=surveymodel#commentPrefix
         */
        $surveyConfiguration = new SurveyConfiguration();
        $surveyConfiguration->commentPostfix = $this->extractString($structure, 'commentPrefix', '-Comment');

        if (isset($structure['pages']) && is_array($structure['pages'])) {
            foreach ($structure['pages'] as $page) {
                foreach ($this->parsePage($page, $surveyConfiguration) as $variable) {
                    $variables[] = $variable;
                }
            }
        }

        return new VariableSet(...$variables);
    }

    public function parseJson(string $json): VariableSetInterface
    {
        $decoded = json_decode($json, true, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new \InvalidArgumentException("JSON string is not an object");
        }
        return $this->parseSurveyStructure($decoded);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function extractString(array $config, string $key, string $defaultValue): string
    {
        if (isset($config[$key]) && is_string($config[$key])) {
            return $key;
        }
        return $defaultValue;
    }
}
