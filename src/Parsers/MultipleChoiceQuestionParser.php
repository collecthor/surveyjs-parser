<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use ValueError;
use function implode;

final class MultipleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode(".", $dataPath);

        $titles = $this->extractTitles($questionConfig);

        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'));


        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? $this->extractOptionalBoolean($questionConfig, 'showNoneItem') ?? false) {
            $choices[] = new NoneValueOption('none', $this->extractLocalizedTexts($questionConfig, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? $this->extractOptionalBoolean($questionConfig, 'showOtherItem') ?? false) {
            $choices[] = new OtherValueOption('other', $this->extractLocalizedTexts($questionConfig, 'otherText'));
        }

        // choicesFromQuestion
        if (isset($questionConfig['choicesFromQuestion']) && is_string($questionConfig['choicesFromQuestion'])) {
            yield new DeferredVariable(
                $name,
                static function (ResolvableVariableSet $set) use ($name, $titles, $dataPath, $questionConfig): VariableInterface {
                    $variable = $set->getVariable($questionConfig['choicesFromQuestion']);
                    if ($variable instanceof ClosedVariableInterface) {
                        $options = $variable->getValueOptions();
                        return new MultipleChoiceVariable($name, $titles, $options, $dataPath, $questionConfig);
                    } else {
                        throw new ValueError("Question {$questionConfig['choicesFromQuestion']} does not implement ClosedQuestionInterface");
                    }
                },
            );
        } else {
            if ($choices === []) {
                throw new \InvalidArgumentException("Choices must not be empty");
            }
            yield new MultipleChoiceVariable($name, $titles, $choices, $dataPath, $questionConfig);
        }
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
