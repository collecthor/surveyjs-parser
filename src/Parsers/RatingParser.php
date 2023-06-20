<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
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


        if (isset($questionConfig['rateValues'])) {
            /** @var non-empty-array<int, ValueOptionInterface<string>> $answers */
            $answers = [];

            /** @var list<mixed> $values */
            $values = $questionConfig['rateValues'];

            foreach ($values as $value) {
                if (is_array($value)) {
                    $texts = $this->extractLocalizedTexts($value);
                    $value = $value['value'];
                }
                $answers[] = new StringValueOption((string) $value, $texts ?? [ 'default' => (string) $value]);
            }
            yield new SingleChoiceVariable($id, $this->extractTitles($questionConfig), $answers, $dataPath);
        } else {
            /** @var non-empty-array<int, ValueOptionInterface<int>> $answers */
            $answers = [];
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
            yield new SingleChoiceVariable($id, $this->extractTitles($questionConfig), $answers, $dataPath);
        }
    }
}
