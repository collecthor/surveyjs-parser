<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

use function Collecthor\SurveyjsParser\Helpers\extractName;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class ExpressionParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, extractName($questionConfig)]);
        $titles = extractTitles($questionConfig);

        // Try to parse the expression.
        if (isset($questionConfig['expression']) && is_string($questionConfig['expression'])) {
            $sub = new \Collecthor\SurveyjsParser\Helpers\ExpressionParser();
            try {
                $questionConfig['parsedExpression'] = $sub->parse($questionConfig['expression']);
            } catch (\Exception $e) {
                $questionConfig['parseError'] = $e->getMessage();
            }
        }

        yield new OpenTextVariable(
            name: $name,
            dataPath: $dataPath,
            titles: $titles,
            rawConfiguration: $questionConfig
        );
    }
}
