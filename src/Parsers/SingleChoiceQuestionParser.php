<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\ClosedVariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\NoneValueOption;
use Collecthor\SurveyjsParser\Values\OtherValueOption;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use ValueError;

class SingleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(
        ElementParserInterface $root,
        array $questionConfig,
        SurveyConfiguration $surveyConfiguration,
        array $dataPrefix = []
    ): iterable {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);
        $titles = $this->extractTitles($questionConfig);

        // Parse the answer options.
        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));

        // Add autogenerated options
        $max = $this->extractOptionalInteger($questionConfig, 'choicesMax');
        if (isset($max)) {
            $min = $this->extractOptionalInteger($questionConfig, 'choicesMin') ?? 0;
            $step = $this->extractOptionalInteger($questionConfig, 'choicesStep') ?? 1;
            for ($i = $min;  $i < $max; $i += $step) {
                $choices[] = new IntegerValueOption($i, ['default' => (string) $i]);
            }
        }

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
                $id,
                static function (ResolvableVariableSet $set) use ($id, $titles, $dataPath, $questionConfig): VariableInterface {
                    $variable = $set->getVariable($questionConfig['choicesFromQuestion']);
                    if ($variable instanceof ClosedVariableInterface) {
                        $options = $variable->getValueOptions();
                        return new SingleChoiceVariable($id, $titles, $options, $dataPath, $questionConfig);
                    } else {
                        throw new ValueError("Question {$questionConfig['choicesFromQuestion']} does not implement ClosedQuestionInterface");
                    }
                },
            );
        } else {
            if ($choices === []) {
                throw new \InvalidArgumentException("Choices must not be empty");
            }
            yield new SingleChoiceVariable($id, $titles, $choices, $dataPath, $questionConfig);
        }
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
