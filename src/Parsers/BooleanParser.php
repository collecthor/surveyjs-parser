<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;

final class BooleanParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $booleanNames = [
            'true' => [
                'default' => 'true',
            ],
            'false' => [
                'default' => 'false',
            ],
        ];
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $valueName = $this->extractValueName($questionConfig);
        yield new BooleanVariable($valueName, $titles, $booleanNames, [...$dataPrefix, $valueName]);
    }
}
