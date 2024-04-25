<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

final readonly class ExpressionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig)]);
        $titles = $this->extractTitles($questionConfig);

        // Try to parse the expression.
        if (isset($questionConfig['expression']) && is_string($questionConfig['expression'])) {
            $sub = new \Collecthor\SurveyjsParser\Helpers\ExpressionParser();
            $sub->parse($questionConfig['expression']);
        }

        yield new OpenTextVariable(
            name: $name,
            dataPath: $dataPath,
            titles: $titles,
            rawConfiguration: $questionConfig
        );
    }
}
