<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Values\StringValueOption;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

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

        if ($choices !== []) {
            yield new MultipleChoiceVariable($name, $titles, $choices, $dataPath, $questionConfig);
        }
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
