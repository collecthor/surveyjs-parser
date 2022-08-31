<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OrderedVariable;

final class OrderedVariableParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = $this->extractName($questionConfig);

        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);

        $choices = $this->extractChoices($this->extractArray($questionConfig, 'choices'), $surveyConfiguration);

        yield new OrderedVariable($name, $titles, $choices, $dataPath, $questionConfig);
    }
}
