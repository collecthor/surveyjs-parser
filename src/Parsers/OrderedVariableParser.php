<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

final class OrderedVariableParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = $this->extractName($questionConfig);

        $titles = $this->extractTitles($questionConfig);

        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));

        if ($choices !== []) {
            yield new MultipleChoiceVariable(
                name: $name,
                dataPath: $dataPath,
                options: $choices,
                titles: $titles,
                rawConfiguration: $questionConfig,
                ordered: true
            );
        }
    }
}
