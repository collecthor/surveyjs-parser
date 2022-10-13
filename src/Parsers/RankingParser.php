<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

final class RankingParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig);
        $valueName = $this->extractValueName($questionConfig);
        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));

        for ($i = 0; $i < count($choices); $i++) {
            yield new SingleChoiceVariable(
                "{$valueName}.{$i}",
                $this->arrayFormat($titles, " ({$i})"),
                $choices,
                [...$dataPrefix, $valueName, (string)$i],
            );
        }
    }
}
