<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\BooleanVariable;

/**
 * @type T bool
 */
final class BooleanParser implements ElementParserInterface
{
    use ParserHelpers;

    /**
     * @param array<string, string> $trueLabels
     * @param array<string, string> $falseLabels
     */
    public function __construct(private readonly array $trueLabels, private readonly array $falseLabels)
    {
    }

    public function parse(ElementParserInterface $root, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig)];
        $id = implode('.', $dataPath);

        $titles = $this->extractTitles($questionConfig);
        yield new BooleanVariable($id, $titles, $this->trueLabels, $this->falseLabels, $dataPath);
    }
}
