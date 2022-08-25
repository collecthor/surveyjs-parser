<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
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
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);
        if (isset($questionConfig['rateValues'])) {
            /** @var non-empty-array<int, ValueOptionInterface> $answers */
            $answers = [];

            /** @var list<mixed> $values */
            $values = $questionConfig['rateValues'];

            foreach ($values as $value) {
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
