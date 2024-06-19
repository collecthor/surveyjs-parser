<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\FloatVariable;

use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class NoUISliderParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $valueName = extractValueName($questionConfig);
        $titles = extractTitles($questionConfig);
        yield new FloatVariable(name: $valueName, titles: $titles, dataPath: [...$dataPrefix, $valueName], rawConfiguration: $questionConfig);
    }
}
