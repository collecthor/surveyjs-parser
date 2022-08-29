<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\IntegerValueOption;
use Collecthor\SurveyjsParser\Values\StringValue;
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

        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig)]);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        // Parse the answer options.
        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'), $surveyConfiguration);

        // Check if we need to add options for `hasNone` or `hasOther`
        if ($this->extractOptionalBoolean($questionConfig, 'hasNone') ?? false) {
            $choices[] = new StringValueOption('none', $this->extractLocalizedTexts($questionConfig, $surveyConfiguration, 'noneText'));
        }

        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? false) {
            $choices[] = new StringValueOption('other', $this->extractLocalizedTexts($questionConfig, $surveyConfiguration, 'otherText'));
        }

        yield new SingleChoiceVariable($name, $titles, $choices, $dataPath, $questionConfig);
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
