<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

class RatingParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $questionConfig['valueName'] ?? $questionConfig['name']];
        $id = implode('.', $dataPath);
        if (isset($questionConfig['rateValues'])) {
            $answers = [];
            foreach ($questionConfig['rateValues'] as $value) {
                if (is_array($value)) {
                    $texts = $this->extractLocalizedTexts($value, $surveyConfiguration);
                    $value = $value['value'];
                }
                $answers[] = new StringValueOption($value, $texts ?? [ 'default' => (string) $value]);
            }
            yield new SingleChoiceVariable($id, $this->extractTitles($questionConfig, $surveyConfiguration), $answers, $dataPath);
        } else {
            yield new NumericVariable($id, $this->extractTitles($questionConfig, $surveyConfiguration), $dataPath);
        }
    }
}
