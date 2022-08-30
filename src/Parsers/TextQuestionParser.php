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
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig)]);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        if (($questionConfig['inputType'] ?? 'text') === 'number') {
            yield new NumericVariable($name, $titles, $dataPath, $questionConfig);
        } else {
            yield new OpenTextVariable($name, $titles, $dataPath, $questionConfig);
        }

        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
