<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

final readonly class BooleanParser implements ElementParserInterface
{
    use ParserHelpers;

    /**
     * @param array<string, string> $trueLabels
     * @param array<string, string> $falseLabels
     */
    public function __construct(private readonly array $trueLabels, private readonly array $falseLabels)
    {
    }

    /**
     * @return iterable<BooleanVariable|OpenTextVariable>
     */
    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);

        $titles = $this->extractTitles($questionConfig);
        yield new BooleanVariable(
            $id,
            $dataPath,
            $titles,
            $this->trueLabels,
            $this->falseLabels,
            $questionConfig,
            trueValue: $this->extractOptionalString($questionConfig, 'valueTrue') ?? true,
            falseValue: $this->extractOptionalString($questionConfig, 'valueFalse') ?? false,
        );
        yield from $this->parseCommentField($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
