<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;

final readonly class PanelParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        foreach (extractOptionalArray($questionConfig, 'elements') ?? [] as $elementConfig) {
            /** @phpstan-ignore argument.type  */
            yield from $root->parse($root, $elementConfig, $surveyConfiguration, $dataPrefix);
        }
    }
}
