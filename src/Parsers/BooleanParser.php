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

    /**
     * @param array<string, array<string, string>> $booleanNames
     * @return self
     */
    public function __construct(private readonly array $booleanNames)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $valueName = $this->extractValueName($questionConfig);
        yield new BooleanVariable($valueName, $titles, $this->booleanNames, [...$dataPrefix, $valueName]);
    }
}
