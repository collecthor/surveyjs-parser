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
        /** @var array{choices: list<string>|array<string, array<string, string>>} $questionConfig */
        $choices = $this->extractChoices($questionConfig['choices']);

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
