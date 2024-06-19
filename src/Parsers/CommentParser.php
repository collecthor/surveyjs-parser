<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\SurveyjsParser\ParserHelpers;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;

final readonly class CommentParser
{
    use ParserHelpers;

    /**
     * @param non-empty-array<mixed> $questionConfig
     * @param list<string> $dataPrefix
     * @return iterable<OpenTextVariable>
     */
    public function parse(array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        if ($this->extractOptionalBoolean($questionConfig, 'hasOther') ?? $this->extractOptionalBoolean($questionConfig, 'showOtherItem') ?? false) {
            $defaultPostfix = "Other";
            $postfixField = "otherText";
        } elseif ($this->extractOptionalBoolean($questionConfig, 'hasComment') ?? $this->extractOptionalBoolean($questionConfig, 'showCommentArea') ?? false) {
            $defaultPostfix = "Other (describe)";
            $postfixField = "commentText";
        } else {
            return;
        }

        $defaultPostfixes = [
            'default' => $defaultPostfix,
        ];

        $postfixes = $this->extractLocalizedTexts($questionConfig, $postfixField, $defaultPostfixes);

        $titles = [];
        foreach ($this->extractTitles($questionConfig) as $locale => $title) {
            $titles[$locale] = $title . " - " . ($postfixes[$locale] ?? $postfixes['default']);
        }


        $name = implode('.', [...$dataPrefix, $this->extractName($questionConfig), 'comment']);
        $dataPath = [...$dataPrefix, $this->extractValueName($questionConfig) . $surveyConfiguration->commentSuffix];

        yield new OpenTextVariable(name: $name, dataPath: $dataPath, titles: $titles, rawConfiguration: $questionConfig);
    }
}
