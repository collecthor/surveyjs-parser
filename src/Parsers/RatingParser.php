<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final class RatingParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);

        /** @var non-empty-array<int, ValueOptionInterface> $answers */
        $answers = [];

        if (isset($questionConfig['rateValues'])) {
            /** @var list<mixed> $values */
            $values = $questionConfig['rateValues'];

            foreach ($values as $value) {
                if (is_array($value)) {
                    $texts = $this->extractLocalizedTexts($value, $surveyConfiguration);
                    $value = $value['value'];
                }
                $answers[] = new StringValueOption($value, $texts ?? [ 'default' => (string) $value]);
            }
        } else {
            /** @var int $rateMin */
            $rateMin = $questionConfig['rateMin'] ?? 1;
            $rateMax = $questionConfig['rateMax'] ?? 5;
            /** @var int $rateStep */
            $rateStep = $questionConfig['rateStep'] ?? 1;

            for ($i = $rateMin; $i <= $rateMax; $i += $rateStep) {
                $answers[] = new IntegerValueOption($i, [
                    'default' => (string) $i,
                ]);
            }
        }

        yield new SingleChoiceVariable($id, $this->extractTitles($questionConfig, $surveyConfiguration), $answers, $dataPath);
    }
}
