<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser;

use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Parsers\BooleanParser;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\DynamicPanelParser;
use Collecthor\SurveyjsParser\Parsers\ExpressionParser;
use Collecthor\SurveyjsParser\Parsers\ImagePickerParser;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\Parsers\MatrixParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceMatrixParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\MultipleTextParser;
use Collecthor\SurveyjsParser\Parsers\NoUISliderParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\Parsers\RankingParser;
use Collecthor\SurveyjsParser\Parsers\RatingParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;

class SurveyParser implements SurveyParserInterface, ElementParserInterface
{
    /**
     * @var array<string, ElementParserInterface>
     */
    private array $parsers = [];

    private ElementParserInterface $defaultParser;

    private ElementParserInterface $recursiveParser;

    public function __construct(LocalizerInterface $localizer = new ParserLocalizer())
    {
        // Recursive parser.
        $this->recursiveParser = new CallbackElementParser(
            function (
                ElementParserInterface $parent,
                array $questionConfig,
                SurveyConfiguration $surveyConfiguration,
                array $dataPrefix
            ) {
                yield from $this->parseElement(config: $questionConfig, surveyConfiguration: $surveyConfiguration, dataPrefix: $dataPrefix);
            }
        );

        // Configure default built-in parsers.
        $textParser = new TextQuestionParser();
        $this->parsers['text'] = $textParser;
        $this->parsers['comment'] = $textParser;
        $this->parsers['expression'] = new ExpressionParser();

        $singleChoiceParser = new SingleChoiceQuestionParser();
        $this->parsers['radiogroup'] = $singleChoiceParser;
        $this->parsers['dropdown'] = $singleChoiceParser;

        $booleanParser = new BooleanParser(
            $localizer->getAllTranslationsForString('true'),
            $localizer->getAllTranslationsForString('false')
        );
        $this->parsers['boolean'] = $booleanParser;

        $imagePickerParser = new ImagePickerParser();
        $this->parsers['imagepicker'] = $imagePickerParser;

        $matrixDynamicParser = new MatrixDynamicParser($localizer->getAllTranslationsForString('row'));
        $this->parsers['matrixdynamic'] = $matrixDynamicParser;

        $multipleChoiceQuestionParser = new MultipleChoiceQuestionParser();
        $this->parsers['checkbox'] = $multipleChoiceQuestionParser;
        $this->parsers['imagemap'] = $multipleChoiceQuestionParser;
        $this->parsers['tagbox'] = $multipleChoiceQuestionParser;

        $multipleTextParser = new MultipleTextParser();
        $this->parsers['multipletext'] = $multipleTextParser;

        $panelParser = new PanelParser();
        $this->parsers['panel'] = $panelParser;

        $rankingParser = new RankingParser();
        $this->parsers['ranking'] = $rankingParser;
        $this->parsers['sortablelist'] = $rankingParser;

        $ratingParser = new RatingParser();
        $this->parsers['rating'] = $ratingParser;

        $noUISliderParser = new NoUISliderParser();
        $this->parsers['nouislider'] = $noUISliderParser;

        $matrixParser = new MatrixParser();
        $this->parsers['matrix'] = $matrixParser;

        $multipleChoiceMatrixParser = new MultipleChoiceMatrixParser();
        $this->parsers['matrixdropdown'] = $multipleChoiceMatrixParser;

        $dynamicPanelParser = new DynamicPanelParser($localizer->getAllTranslationsForString('row'));
        $this->parsers['paneldynamic'] = $dynamicPanelParser;

        $dummyParser = new DummyParser();
        $this->parsers['html'] = $dummyParser;
        $this->parsers['image'] = $dummyParser;
        $this->defaultParser = $dummyParser;
    }

    /**
     * Sets the parser for a specific question, removes all other registered parser for this question parser.
     * @param string $type
     * @param ElementParserInterface $parser
     * @return void
     */
    public function setParser(string $type, ElementParserInterface $parser): void
    {
        $this->parsers[$type] = $parser;
    }

    public function setDefaultParser(ElementParserInterface $parser): void
    {
        $this->defaultParser = $parser;
    }

    private function getParser(string $type): ElementParserInterface
    {
        return $this->parsers[$type] ?? $this->defaultParser;
    }

    /**
     * @param array<mixed> $config
     * @param SurveyConfiguration $surveyConfiguration
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface|DeferredVariable>
     */
    private function parseElement(array $config, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if (!is_string($config['type'])) {
            throw new \ParseError("Element JSON must contain 'type' key and it must be a string");
        }

        yield from $this->getParser($config['type'])->parse(root: $this->recursiveParser, questionConfig: $config, surveyConfiguration: $surveyConfiguration, dataPrefix: $dataPrefix);
    }

    /**
     * @phpstan-param array<mixed> $structure
     * @param SurveyConfiguration $surveyConfiguration
     * @return iterable<VariableInterface|DeferredVariable>
     */
    private function parsePage(array $structure, SurveyConfiguration $surveyConfiguration): iterable
    {
        $elements = isset($structure['elements']) && is_iterable($structure['elements']) ? $structure['elements'] : [];
        foreach ($elements as $element) {
            if (!is_array($element)) {
                throw new ParseError("Element JSON must be a dictionary");
            }
            yield from $this->parseElement($element, $surveyConfiguration);
        }
    }

    /**
     * @param array<string|int, mixed> $structure
     */
    public function parseSurveyStructure(array $structure): VariableSetInterface
    {
        $variables = [];

        /**
         * Get some global settings from the survey structure. Note surveyJS incorrectly calls this a prefix
         * https://surveyjs.io/Documentation/Library?id=surveymodel#commentPrefix
         */
        $surveyConfiguration = new SurveyConfiguration();

        if (is_array($structure['pages'] ?? null)) {
            foreach ($structure['pages'] as $page) {
                if (!is_array($page)) {
                    throw new ParseError("Page JSON must be a dictionary");
                }
                foreach ($this->parsePage($page, $surveyConfiguration) as $variable) {
                    $variables[] = $variable;
                }
            }
        }
        $resolvable = new ResolvableVariableSet(...$variables);

        /**
         * Parse calculated values
         */
        if (is_array($structure['calculatedValues'] ?? null)) {
            foreach ($structure['calculatedValues'] as $calculatedValue) {
                if (is_array($calculatedValue) && isset($calculatedValue['includeIntoResult'], $calculatedValue['name'])
                    && $calculatedValue['includeIntoResult'] === true && is_string($calculatedValue['name'])
                ) {
                    foreach ($this->getParser('expression')->parse(
                        root: $this->recursiveParser,
                        questionConfig: $calculatedValue,
                        surveyConfiguration: $surveyConfiguration
                    ) as $variable) {
                        $variables[] = $variable;
                    }
                }
            }
        }

        $resolvedVariables = [];
        foreach ($variables as $variable) {
            if ($variable instanceof DeferredVariable) {
                $resolvedVariables[] = $variable->resolve($resolvable);
            } else {
                $resolvedVariables[] = $variable;
            }
        }

        return new VariableSet(...$resolvedVariables);
    }

    public function parseJson(string $json): VariableSetInterface
    {
        $decoded = json_decode($json, true, JSON_THROW_ON_ERROR);
        if (!is_array($decoded) || array_is_list($decoded)) {
            throw new \InvalidArgumentException("JSON string is not an object");
        }
        return $this->parseSurveyStructure($decoded);
    }

    public function parse(
        ElementParserInterface $root,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        return $this->recursiveParser->parse($root, $questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
