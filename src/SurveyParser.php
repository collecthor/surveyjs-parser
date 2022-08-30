<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Parsers\BooleanParser;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\ImageFeedbackParser;
use Collecthor\SurveyjsParser\Parsers\MatrixDynamicParser;
use Collecthor\SurveyjsParser\Parsers\MatrixParser;
use Collecthor\SurveyjsParser\Parsers\MultipleChoiceParser;
use Collecthor\SurveyjsParser\Parsers\MultipleTextParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\Parsers\RankingParser;
use Collecthor\SurveyjsParser\Parsers\RatingParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;

class SurveyParser implements SurveyParserInterface
{
    /**
     * @var array<string, list<ElementParserInterface>>
     */
    private array $parsers = [];

    private ElementParserInterface $defaultParser;

    private ElementParserInterface $recursiveParser;

    public function __construct(ParserLocalizer $localizer = new ParserLocalizer())
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
        $this->parsers['text'] = [$textParser];
        $this->parsers['comment'] = [$textParser];
        $this->parsers['expression'] = [$textParser];

        $singleChoiceParser = new SingleChoiceQuestionParser();
        $this->parsers['radiogroup'] = [$singleChoiceParser];
        $this->parsers['dropdown'] = [$singleChoiceParser];
        $this->parsers['barrating'] = [$singleChoiceParser];

        $multipleChoiceParser = new MultipleChoiceParser();
        $this->parsers['checkbox'] = [$multipleChoiceParser];
        $this->parsers['imagemap'] = [$multipleChoiceParser];
        $this->parsers['tagbox'] = [$multipleChoiceParser];

        $ratingParser = new RatingParser();
        $this->parsers['rating'] = [$ratingParser];

        $multipleTextParser = new MultipleTextParser();
        $this->parsers['multipletext'] = [$multipleTextParser];

        $matrixDynamicParser = new MatrixDynamicParser($localizer->getAllTranslationsForString('row'));
        $this->parsers['matrixdynamic'] = [$matrixDynamicParser];

        $imageFeedbackParser = new ImageFeedbackParser(
            $localizer->getAllTranslationsForString('positive'),
            $localizer->getAllTranslationsForString('text'),
            $localizer->getAllTranslationsForString('true'),
            $localizer->getAllTranslationsForString('false'),
        );
        $this->parsers['imagefeedback'] = [$imageFeedbackParser];

        $matrixParser = new MatrixParser();
        $this->parsers['matrix'] = [$matrixParser];

        $booleanParser = new BooleanParser(
            $localizer->getAllTranslationsForString('true'),
            $localizer->getAllTranslationsForString('false'),
        );
        $this->parsers['boolean'] = [$booleanParser];

        $rankingParser = new RankingParser();
        $this->parsers['ranking'] = [$rankingParser];
        $this->parsers['sortablelist'] = [$rankingParser];

        $dummyParser = new DummyParser();
        $this->parsers['panel'] = [new PanelParser()];
        $this->parsers['html'] = [$dummyParser];
        $this->parsers['image'] = [$dummyParser];
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
        $this->parsers[$type] = [$parser];
    }

    public function addParser(string $type, ElementParserInterface $parser): void
    {
        $this->parsers[$type][] = $parser;
    }

    public function setDefaultParser(ElementParserInterface $parser): void
    {
        $this->defaultParser = $parser;
    }

    /**
     * @param string $type
     * @return list<ElementParserInterface>
     */
    private function getParsers(string $type): array
    {
        return $this->parsers[$type] ?? [$this->defaultParser];
    }

    /**
     * @phpstan-param array{type: string} $config
     * @param SurveyConfiguration $surveyConfiguration
     * @param list<string> $dataPrefix
     * @return iterable<VariableInterface>
     */
    private function parseElement(array $config, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        foreach ($this->getParsers($config['type']) as $parser) {
            yield from $parser->parse($this->recursiveParser, $config, $surveyConfiguration, $dataPrefix);
        }
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
