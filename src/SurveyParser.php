<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
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
            function (
                ElementParserInterface $parent,
                SurveyConfiguration $surveyConfiguration,
                array $questionConfig,
                array $dataPrefix = []
            ) {
                /** @phpstan-ignore-next-line */
                yield from $this->parseElement($questionConfig, $surveyConfiguration, $dataPrefix);
            }
        );

        // Configure default built-in parsers.
        $textParser = new TextQuestionParser();
        $this->parsers['text'] = $textParser;
        $this->parsers['comment'] = $textParser;
        $this->parsers['expression'] = $textParser;

        $singleChoiceParser = new SingleChoiceQuestionParser();
        $this->parsers['radiogroup'] = $singleChoiceParser;
        $this->parsers['dropdown'] = $singleChoiceParser;

        $this->parsers['panel'] = new PanelParser();
        $this->parsers['html'] = new DummyParser();
        $this->parsers['image'] = new DummyParser();
        $this->parsers[self::UNKNOWN_ELEMENT_TYPE] = new DummyParser();
    }

    /**
     * Sets a parser for a question type. Will override the previously configured or default parser.
     * @param string $type
     * @param ElementParserInterface $parser
     * @return void
     */
    public function setParser(string $type, ElementParserInterface $parser): void
    {
        $this->parsers[$type] = $parser;
    }

    private function getParser(string $type): ElementParserInterface
    {
        return $this->parsers[$type] ?? $this->parsers[self::UNKNOWN_ELEMENT_TYPE];
    }

    /**
     * @phpstan-param array{type: string} $config
     * @param SurveyConfiguration $surveyConfiguration
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface>
     */
    private function parseElement(array $config, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        yield from $this->getParser($config['type'])->parse($this->recursiveParser, $config, $surveyConfiguration, $dataPrefix);
    }

    /**
     * @phpstan-param array{elements: non-empty-list<array{"type": string}>} $structure
     * @param SurveyConfiguration $surveyConfiguration
     * @return iterable<VariableInterface>
     */
    private function parsePage(array $structure, SurveyConfiguration $surveyConfiguration): iterable
    {
        foreach ($structure['elements'] as $element) {
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
        $surveyConfiguration = new SurveyConfiguration($this->extractString($structure, 'commentPrefix', '-Comment'));

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
