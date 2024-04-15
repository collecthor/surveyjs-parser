<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

final class RankingParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig);
        $valueName = $this->extractValueName($questionConfig);
        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));
        if ($choices === []) {
            return;
        }

        yield new MultipleChoiceVariable(
            name: $valueName,
            dataPath: [...$dataPrefix, $valueName],
            options: $choices,
            titles: $titles,
            rawConfiguration: $questionConfig,
            ordered: true
        );
        yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
