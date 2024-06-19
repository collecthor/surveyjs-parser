<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\Interfaces\IntegerValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceIntegerVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

use function Collecthor\SurveyjsParser\Helpers\allInstanceOf;
use function Collecthor\SurveyjsParser\Helpers\extractLocalizedTexts;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalInteger;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class RatingParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, extractValueName($questionConfig)];
        $id = implode('.', $dataPath);


        if (isset($questionConfig['rateValues'])) {
            $answers = [];

            foreach (extractOptionalArray($questionConfig, 'rateValues') ?? [] as $value) {
                if (is_array($value)) {
                    if (!is_scalar($value['value'])) {
                        throw new ParseError('Rate value must be scalar');
                    }
                    if (is_int($value['value']) || ctype_digit($value['value'])) {
                        $answers[] = new IntegerValueOption(
                            rawValue: intval($value['value']),
                            displayValues: extractLocalizedTexts($value)
                        );
                    } else {
                        $answers[] = new StringValueOption(
                            rawValue: (string) $value['value'],
                            displayValues: extractLocalizedTexts($value)
                        );
                    }
                } elseif (is_int($value) || ctype_digit($value)) {
                    $answers[] = new IntegerValueOption(
                        rawValue: intval($value),
                        displayValues: ['default' => (string) $value]
                    );
                } elseif (is_string($value)) {
                    $answers[] = new StringValueOption(
                        rawValue: $value,
                        displayValues: ['default' => $value]
                    );
                }
            }
            if ($answers === []) {
                throw new ParseError('Rating question has no values');
            }

            if (allInstanceOf($answers, IntegerValueOptionInterface::class)) {
                yield new SingleChoiceIntegerVariable(
                    name: $id,
                    titles: extractTitles($questionConfig),
                    options: $answers,
                    dataPath: $dataPath,
                    rawConfiguration: $questionConfig,
                    measure: Measure::Ordinal
                );
            } else {
                yield new SingleChoiceVariable(
                    name: $id,
                    options: $answers,
                    dataPath: $dataPath,
                    rawConfiguration: $questionConfig,
                    titles: extractTitles($questionConfig),
                    measure: Measure::Ordinal
                );
            }
        } else {
            $answers = [];
            $rateMin = extractOptionalInteger($questionConfig, 'rateMin') ?? 1;
            $rateMax = extractOptionalInteger($questionConfig, 'rateMax') ?? 5;
            $rateStep = extractOptionalInteger($questionConfig, 'rateStep') ?? 1;

            for ($i = $rateMin; $i <= $rateMax; $i += $rateStep) {
                $answers[] = new IntegerValueOption($i, [
                    'default' => (string) $i,
                ]);
            }
            if ($answers !== []) {
                yield new SingleChoiceIntegerVariable(
                    name: $id,
                    titles: extractTitles($questionConfig),
                    options: $answers,
                    dataPath: $dataPath,
                    measure: !isset($questionConfig['rateType']) ? Measure::Scale : Measure::Ordinal
                );
            }
            yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
