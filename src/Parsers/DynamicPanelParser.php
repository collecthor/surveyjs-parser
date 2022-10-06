<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;

final class DynamicPanelParser implements ElementParserInterface
{
    use ParserHelpers;

    /**
     * @param array<string, string> $rowLabels
     */
    public function __construct(private array $rowLabels)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $titles = $this->extractTitles($questionConfig);
        $limit = $questionConfig['maxPanelCount'] ?? 10;

        for ($r = 0; $r < $limit; $r++) {
            foreach ((array)($questionConfig['templateElements'] ?? []) as $element) {
                /** @var array<string, mixed> $rowElement */
                $rowElement = $element;
                $valueName = $this->extractValueName($rowElement);
                $rowTitles = $this->extractTitles($rowElement);
                $rowElement['title'] = $this->arrayFormat($titles, " ", $this->rowLabels, " ", (string)$r, " ", $rowTitles);
                $rowElement['name'] = implode('.', [...$dataPrefix, $valueName, (string)$r]);
                yield from $root->parse($root, $rowElement, $surveyConfiguration, $dataPrefix);
            }
        }
    }
}
