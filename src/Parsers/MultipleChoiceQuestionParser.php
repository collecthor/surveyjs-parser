<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ClosedVariableInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use InvalidArgumentException;

final class MultipleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = $this->extractName($questionConfig);

        $titles = $this->extractTitles($questionConfig);

        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'));

        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? false) {
            $choices[] = new StringValueOption('none', $this->extractLocalizedTexts($questionConfig, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            $choices[] = new StringValueOption('other', $this->extractLocalizedTexts($questionConfig, 'otherText'));
        }
        // choicesFromQuestion
        if (isset($questionConfig['choicesFromQuestion']) && is_string($questionConfig['choicesFromQuestion'])) {
            yield new DeferredVariable(
                $name,
                function (ResolvableVariableSet $set) use ($name, $titles, $dataPath, $questionConfig): VariableInterface {
                    /** @var ClosedVariableInterface $variable */
                    $variable = $set->getVariable($questionConfig['choicesFromQuestion']);
                    $options = $variable->getValueOptions();
                    if (count($options) === 0) {
                        throw new InvalidArgumentException("Options of variable {$questionConfig['choicesFromQuestion']} are empty, parsing {$name} failed");
                    }
                    return new MultipleChoiceVariable($name, $titles, $options, $dataPath, $questionConfig);
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
