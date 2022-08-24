<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;

class ImagePickerParser implements ElementParserInterface
{
    public function __construct(
        private SingleChoiceQuestionParser $singleChoiceParser,
        private MultipleChoiceParser $multipleChoiceParser
    ) {
    }
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if ($questionConfig['multiSelect'] ?? false) {
            yield from $this->multipleChoiceParser->parse($root, $questionConfig, $surveyConfiguration, $dataPrefix);
        } else {
            yield from $this->singleChoiceParser->parse($root, $questionConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
