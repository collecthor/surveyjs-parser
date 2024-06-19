<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

use function Collecthor\SurveyjsParser\Helpers\arrayFormat;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class DynamicPanelParser implements ElementParserInterface
{
    /**
     * @param array<string, string> $rowLabels
     */
    public function __construct(private array $rowLabels)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = extractTitles($questionConfig);
        $limit = $questionConfig['maxPanelCount'] ?? 10;

        for ($r = 0; $r < $limit; $r++) {
            foreach ((array)($questionConfig['templateElements'] ?? []) as $element) {
                /** @var array<string, mixed> $rowElement */
                $rowElement = $element;
                $valueName = extractValueName($rowElement);
                $rowTitles = extractTitles($rowElement);
                $rowElement['title'] = arrayFormat($titles, " ", $this->rowLabels, " ", (string)$r, " ", $rowTitles);
                $rowElement['name'] = implode('.', [...$dataPrefix, $valueName, (string)$r]);
                yield from $root->parse($root, $rowElement, $surveyConfiguration, $dataPrefix);
            }
        }
    }
}
