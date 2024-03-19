<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Exception\ParseError;
use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\SpecialValueInterface;
use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use function implode;

final readonly class MultipleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode(".", $dataPath);

        $titles = $this->extractTitles($questionConfig);


        // choicesFromQuestion
        if (null !== $choicesFromQuestion = $this->extractOptionalString($questionConfig, 'choicesFromQuestion')) {
            yield new DeferredVariable(
                $name,
                static function (ResolvableVariableSet $set) use ($name, $titles, $dataPath, $questionConfig, $choicesFromQuestion): VariableInterface {
                    $variable = $set->getVariable($choicesFromQuestion);
                    if ($variable instanceof ClosedVariableInterface) {
                        return new MultipleChoiceVariable(
                            name: $name,
                            dataPath: $dataPath,
                            options: array_filter($variable->getOptions(), function (ValueOptionInterface $option) {
                                return !$option instanceof SpecialValueInterface;
                            }),
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
                options: $this->generateChoices($questionConfig),
                titles: $titles,
                rawConfiguration: $questionConfig
            );
        }
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
