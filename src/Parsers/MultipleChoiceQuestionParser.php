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
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use Exception;
use ValueError;
use function implode;
use function is_string;

final class MultipleChoiceQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode(".", $dataPath);

        $titles = $this->extractTitles($questionConfig);

        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'));

        if (!$surveyConfiguration->storeOthersAsComment && isset($questionConfig['choicesFromQuestion']) && is_string($questionConfig['choicesFromQuestion'])) {
            throw new Exception('The combination of choicesFromQuestion and storeOthersAsComment is not supported yet');
        }

        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? false) {
            $choices[] = new StringValueOption('none', $this->extractLocalizedTexts($questionConfig, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            if (!$surveyConfiguration->storeOthersAsComment) {
                yield new OpenTextVariable($name, $titles, $dataPath, $questionConfig);
                return;
            }
            $choices[] = new StringValueOption('other', $this->extractLocalizedTexts($questionConfig, 'otherText'));
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
