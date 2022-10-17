<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\DeferredVariable;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

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
        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'));

        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? false) {
            $choices[] = new StringValueOption('none', $this->extractLocalizedTexts($questionConfig, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            $choices[] = new StringValueOption('other', $this->extractLocalizedTexts($questionConfig, 'otherText'));
        }

        // choicesFromQuestion 
        if (isset($questionConfig['choicesFromQuestion'])) {
            yield new DeferredVariable($id, 
                fn(VariableSetInterface $set) => yield new SingleChoiceVariable($id, $titles, $set->getVariable($id)->getChoices(), $dataPath, $questionConfig),
            );
        } else {

            yield new SingleChoiceVariable($id, $titles, $choices, $dataPath, $questionConfig);
        }
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
