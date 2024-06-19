<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

use function Collecthor\SurveyjsParser\Helpers\extractOptionalString;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;

final readonly class BooleanParser implements ElementParserInterface
{
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
        $dataPath = [...$dataPrefix, extractValueName($questionConfig)];
        $id = implode('.', $dataPath);

        $titles = extractTitles($questionConfig);
        yield new BooleanVariable(
            $id,
            $dataPath,
            $titles,
            $this->trueLabels,
            $this->falseLabels,
            $questionConfig,
            trueValue: extractOptionalString($questionConfig, 'valueTrue') ?? true,
            falseValue: extractOptionalString($questionConfig, 'valueFalse') ?? false,
        );
        yield from (new CommentParser())->parse($questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
