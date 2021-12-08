<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

class CommentParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $parent, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if (($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false)
            || ($this->extractOptionalBoolean($questionConfig, 'hasComment') ?? false)
        ) {
            $titles = $this->extractTitles($questionConfig, $surveyConfiguration);


            $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig), 'comment']);
            $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig) . $surveyConfiguration->commentPostfix];


            yield new OpenTextVariable($name, $titles, $dataPath);
        }
    }
}
