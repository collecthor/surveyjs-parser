<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;

use function Collecthor\SurveyjsParser\Helpers\extractChoices;
use function Collecthor\SurveyjsParser\Helpers\extractName;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalArray;
use function Collecthor\SurveyjsParser\Helpers\extractOptionalBoolean;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class ImagePickerParser implements ElementParserInterface
{
    /**
     * @return iterable<VariableInterface>
     */
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $name = extractName($questionConfig);
        $titles = extractTitles($questionConfig);

        $choices = extractChoices(extractOptionalArray($questionConfig, 'choices'));
        $valueName = extractValueName($questionConfig);

        if ($choices === []) {
            return;
        }

        if (extractOptionalBoolean($questionConfig, 'multiSelect') ?? false) {
            yield new MultipleChoiceVariable(
                name: $name,
                dataPath: [...$dataPrefix, $valueName],
                options: $choices,
                titles: $titles,
                rawConfiguration: $questionConfig
            );
        } else {
            yield new SingleChoiceVariable(
                name: $name,
                options: $choices,
                dataPath: [...$dataPrefix, $valueName],
                rawConfiguration: $questionConfig,
                titles: $titles
            );
        }

        yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
