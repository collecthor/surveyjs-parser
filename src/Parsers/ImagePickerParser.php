<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\MultipleChoiceVariable;
use Collecthor\SurveyjsParser\Variables\SingleChoiceVariable;
use function PHPStan\dumpType;

final class ImagePickerParser implements ElementParserInterface
{
    use ParserHelpers;

    /**
     * @return iterable<VariableInterface>
     */
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $name = $this->extractName($questionConfig);
        $titles = $this->extractTitles($questionConfig);

        $choices = $this->extractChoices($this->extractOptionalArray($questionConfig, 'choices'));
        $valueName = $this->extractValueName($questionConfig);

        if ($choices === []) {
            return;
        }

        if ($this->extractOptionalBoolean($questionConfig, 'multiSelect') ?? false) {
            yield new MultipleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        } else {
            yield new SingleChoiceVariable($name, $titles, $choices, [...$dataPrefix, $valueName]);
        }

        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
