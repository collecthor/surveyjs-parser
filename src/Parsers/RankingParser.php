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
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $valueName = $this->extractValueName($questionConfig);
        /** @var array{choices: list<string>|array<string, array<string, string>>} $questionConfig */
        $choices = $this->extractChoices($questionConfig['choices'], $surveyConfiguration);

        $questionIndex = 0;
        foreach ($choices as $choice) {
            yield new SingleChoiceVariable(
                "{$valueName}.{$questionIndex}",
                $this->arrayFormat($surveyConfiguration, $titles, " ({$questionIndex})"),
                $choices,
                [...$dataPrefix, $valueName, (string)$questionIndex],
            );
            $questionIndex++;
        }
    }
}
