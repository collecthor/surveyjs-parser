<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\FloatVariable;
use Collecthor\SurveyjsParser\Variables\IntegerVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

class TextQuestionParser implements ElementParserInterface
{
    use ParserHelpers;

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];

        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig)]);
        $titles = $this->extractTitles($questionConfig);

        if (($questionConfig['inputType'] ?? 'text') === 'number') {
            // Type depends on whether step, min or max contain decimals.
            if (is_float($questionConfig['min'] ?? null)
                || is_float($questionConfig['max'] ?? null)
                || is_float($questionConfig['step'] ?? null)) {
                yield new FloatVariable(name: $name, titles: $titles, dataPath: $dataPath, rawConfiguration: $questionConfig);
            } else {
                yield new IntegerVariable(
                    name: $name,
                    titles: $titles,
                    dataPath: $dataPath,
                    rawConfiguration: $questionConfig
                );
            }
        } else {
            yield new OpenTextVariable(
                name: $name,
                dataPath: $dataPath,
                titles: $titles,
                rawConfiguration: $questionConfig
            );
        }

        yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
