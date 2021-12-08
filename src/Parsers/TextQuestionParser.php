<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

class TextQuestionParser implements ElementParserInterface
{
    public function __construct(
        private CommentParser $commentParser
    ) {
    }

    use ParserHelpers;
    public function parse(ElementParserInterface $parent, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        /** @phpstan-var non-empty-list<string> $dataPath */
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $questionConfig['name']]);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        if (($questionConfig['inputType'] ?? 'text') === 'number') {
            yield new NumericVariable($name, $titles, $dataPath);
        } else {
            yield new OpenTextVariable($name, $titles, $dataPath);
        }

        // Check if we have a comment field.
        yield from $this->commentParser->parse($parent, $questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
