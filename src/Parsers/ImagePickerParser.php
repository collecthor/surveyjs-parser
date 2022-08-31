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
        /** @var array<mixed> $rawChoices */
        $rawChoices = $questionConfig['choices'];
        $formattedChoices = [];
        /** @var array{value: string, text?: string} $choice*/
        foreach ($rawChoices as $choice) {
            if (!isset($choice['text'])) {
                $formattedChoices[] = [
                    'text' => $choice['value'],
                    'value' => $choice['value'],
                ];
            } else {
                $formattedChoices[] = $choice;
            }
        }
        $name = $this->extractName($questionConfig);
        $titles = $this->extractTitles($questionConfig, $surveyConfiguration);
        $choices = $this->extractChoices($formattedChoices, $surveyConfiguration);
        $valueName = $this->extractValueName($questionConfig);


        if (isset($questionConfig['multiSelect']) && is_bool($questionConfig['multiSelect']) && $questionConfig['multiSelect']) {
            yield new MultipleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        } else {
            yield new SingleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        }
    }
}
