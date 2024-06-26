<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

use function Collecthor\SurveyjsParser\Helpers\extractOptionalString;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;
use function Collecthor\SurveyjsParser\Helpers\generateChoices;
use function implode;

final readonly class MultipleChoiceQuestionParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, extractValueName($questionConfig)];

        $name = implode(".", $dataPath);

        $titles = extractTitles($questionConfig);


        // choicesFromQuestion
        if (null !== $choicesFromQuestion = extractOptionalString($questionConfig, 'choicesFromQuestion')) {
            yield new DeferredVariable(
                $name,
                function (ResolvableVariableSet $set) use ($name, $titles, $dataPath, $questionConfig, $choicesFromQuestion): VariableInterface {
                    $variable = $set->getVariable($choicesFromQuestion);
                    if ($variable instanceof ClosedVariableInterface) {
                        // We get the special choices from current config
                        $specialChoices = array_filter(generateChoices($questionConfig), function (ValueOptionInterface $option) {
                            return $option instanceof SpecialValueInterface;
                        });
                        // We get the normal choices from the referenced variable
                        $normalChoices = array_filter($variable->getOptions(), function (ValueOptionInterface $option) {
                            return !$option instanceof SpecialValueInterface;
                        });

                        return new MultipleChoiceVariable(
                            name: $name,
                            dataPath: $dataPath,
                            options: [...$normalChoices, ...$specialChoices],
                            titles: $titles,
                            rawConfiguration: $questionConfig
                        );
                    } else {
                        throw new ParseError("Question {$choicesFromQuestion} is not a closed question");
                    }
                },
            );
        } else {
            yield new MultipleChoiceVariable(
                name: $name,
                dataPath: $dataPath,
                options: generateChoices($questionConfig),
                titles: $titles,
                rawConfiguration: $questionConfig
            );
        }
        yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
