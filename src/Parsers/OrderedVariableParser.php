<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;

use function Collecthor\SurveyjsParser\Helpers\extractChoices;
use function Collecthor\SurveyjsParser\Helpers\extractName;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class OrderedVariableParser implements ElementParserInterface
{
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, extractValueName($questionConfig)];

        $name = extractName($questionConfig);

        $titles = extractTitles($questionConfig);

        $choices = extractChoices(extractOptionalArray($questionConfig, 'choices'));

        if ($choices !== []) {
            yield new MultipleChoiceVariable(
                name: $name,
                dataPath: $dataPath,
                options: $choices,
                titles: $titles,
                rawConfiguration: $questionConfig,
                ordered: true
            );
        }
    }
}
