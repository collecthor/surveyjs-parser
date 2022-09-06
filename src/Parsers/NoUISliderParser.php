<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\NumericVariable;

final class NoUISliderParser implements ElementParserInterface
{
    use ParserHelpers;
    
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $valueName = $this->extractValueName($questionConfig);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        yield new NumericVariable($valueName, $titles, [...$dataPrefix, $valueName]);
    }
}
