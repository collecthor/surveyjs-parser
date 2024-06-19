<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

use function Collecthor\SurveyjsParser\Helpers\extractLocalizedTexts;
use function Collecthor\SurveyjsParser\Helpers\extractName;
use function Collecthor\SurveyjsParser\Helpers\extractTitles;
use function Collecthor\SurveyjsParser\Helpers\extractValueName;
use function Collecthor\SurveyjsParser\Helpers\showCommentArea;
use function Collecthor\SurveyjsParser\Helpers\showOtherItem;

final readonly class CommentParser
{
    /**
     * @param non-empty-array<mixed> $questionConfig
     * @param list<string> $dataPrefix
     * @return iterable<OpenTextVariable>
     */
    public function parse(array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if (showOtherItem($questionConfig)) {
            $defaultPostfix = "Other";
            $postfixField = "otherText";
        } elseif (showCommentArea($questionConfig)) {
            $defaultPostfix = "Other (describe)";
            $postfixField = "commentText";
        } else {
            return;
        }

        $defaultPostfixes = [
            'default' => $defaultPostfix,
        ];

        $postfixes = extractLocalizedTexts($questionConfig, $postfixField, $defaultPostfixes);

        $titles = [];
        foreach (extractTitles($questionConfig) as $locale => $title) {
            $titles[$locale] = $title . " - " . ($postfixes[$locale] ?? $postfixes['default']);
        }


        $name = implode('.', [...$dataPrefix, extractName($questionConfig), 'comment']);
        $dataPath = [...$dataPrefix, extractValueName($questionConfig) . $surveyConfiguration->commentSuffix];

        yield new OpenTextVariable(name: $name, dataPath: $dataPath, titles: $titles, rawConfiguration: $questionConfig);
    }
}
