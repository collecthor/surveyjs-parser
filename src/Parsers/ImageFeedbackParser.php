<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

final class ImageFeedbackParser implements ElementParserInterface
{
    use ParserHelpers;

    /**
     * @param array<string, string> $positiveLabels
     * @param array<string, string> $textLabels
     * @param array<string,array<string, string>> $booleanNames
     * @return void
     */
    public function __construct(private array $positiveLabels, private array $textLabels, private array $booleanNames)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $name = implode('.', $dataPath);
        $maxItems = $questionConfig['max'] ?? 10;
        for ($i = 0; $i < $maxItems; $i++) {
            yield new NumericVariable("{$name}.{$i}.x", $this->arrayFormat($surveyConfiguration, $titles, " X($i)"), [...$dataPath, (string)$i, 'x']);
            yield new NumericVariable("{$name}.{$i}.y", $this->arrayFormat($surveyConfiguration, $titles, " Y($i)"), [...$dataPath, (string)$i, 'y']);
            yield new BooleanVariable("{$name}.{$i}.positive", $this->arrayFormat($surveyConfiguration, $titles, " ", $this->positiveLabels, " ($i)"), $this->booleanNames, [...$dataPath, (string)$i, 'positive']);
            yield new OpenTextVariable("{$name}.{$i}.text", $this->arrayFormat($surveyConfiguration, $titles, " ", $this->textLabels, " ($i)"), [...$dataPath, (string)$i, 'text']);
        }
    }
}
