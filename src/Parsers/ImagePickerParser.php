<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

use function iter\map;
use function iter\toArray;

final class ImagePickerParser implements ElementParserInterface
{
    use ParserHelpers;
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        /** @var list<array<string, string>> $rawChoices */
        $rawChoices = $questionConfig['choices'];
        $name = $this->extractName($questionConfig);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $choices = $this->extractChoices(
            toArray(map(fn ($choice) => $choice['value'], $rawChoices)),
            $surveyConfiguration
        );
        $valueName = $this->extractValueName($questionConfig);


        if (isset($questionConfig['multiSelect']) && is_bool($questionConfig['multiSelect']) && $questionConfig['multiSelect']) {
            yield new MultipleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        } else {
            yield new SingleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        }
    }
}
