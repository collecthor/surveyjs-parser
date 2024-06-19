<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\Interfaces\IntegerValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\Measure;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceIntegerVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final readonly class RatingParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);


        if (isset($questionConfig['rateValues'])) {
            $answers = [];

            foreach ($this->extractOptionalArray($questionConfig, 'rateValues') ?? [] as $value) {
                if (is_array($value)) {
                    if (!is_scalar($value['value'])) {
                        throw new ParseError('Rate value must be scalar');
                    }
                    if (is_int($value['value']) || ctype_digit($value['value'])) {
                        $answers[] = new IntegerValueOption(
                            rawValue: intval($value['value']),
                            displayValues: $this->extractLocalizedTexts($value)
                        );
                    } else {
                        $answers[] = new StringValueOption(
                            rawValue: (string) $value['value'],
                            displayValues: $this->extractLocalizedTexts($value)
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

            if ($this->allInstanceOf($answers, IntegerValueOptionInterface::class)) {
                yield new SingleChoiceIntegerVariable(
                    name: $id,
                    titles: $this->extractTitles($questionConfig),
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
                    titles: $this->extractTitles($questionConfig),
                    measure: Measure::Ordinal
                );
            }
        } else {
            $answers = [];
            $rateMin = $this->extractOptionalInteger($questionConfig, 'rateMin') ?? 1;
            $rateMax = $this->extractOptionalInteger($questionConfig, 'rateMax') ?? 5;
            $rateStep = $this->extractOptionalInteger($questionConfig, 'rateStep') ?? 1;

            for ($i = $rateMin; $i <= $rateMax; $i += $rateStep) {
                $answers[] = new IntegerValueOption($i, [
                    'default' => (string) $i,
                ]);
            }
            if ($answers !== []) {
                yield new SingleChoiceIntegerVariable(
                    name: $id,
                    titles: $this->extractTitles($questionConfig),
                    options: $answers,
                    dataPath: $dataPath,
                    measure: !isset($questionConfig['rateType']) ? Measure::Scale : Measure::Ordinal
                );
            }
            yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
