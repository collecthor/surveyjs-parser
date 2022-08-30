<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

final class ParserLocalizer implements LocalizerInterface
{
    /** @var array<string, string> $rowLabels */
    private array $rowLabels;
    /** @var array<string, string> $positiveLabels */
    private array $positiveLabels;
    /** @var array<string, string> $textLabels */
    private array $textLabels;
    /** @var array<string, string> $trueLabels */
    private array $trueLabels;
    /** @var array<string, string> $falseLabels */
    private array $falseLabels;

    /**
     * Create a new ParserLocalizer
     * @param null|array<string, string> $rowLabels
     * @param null|array<string, string> $positiveLabels
     * @param null|array<string, string> $textLabels
     * @param null|array<string, string> $trueLabels
     * @param null|array<string, string> $falseLabels
     * @return self
     */
    public function __construct(
        ?array $rowLabels = null,
        ?array $positiveLabels = null,
        ?array $textLabels = null,
        ?array $trueLabels = null,
        ?array $falseLabels = null,
    ) {
        $this->rowLabels = $rowLabels ?? [
            "default" => "Row",
        ];

        $this->positiveLabels = $positiveLabels ?? [
            "default" => "Positive",
        ];

        $this->textLabels = $textLabels ?? [
            "default" => "Text",
        ];

        $this->trueLabels = $trueLabels ?? [
            "default" => "True",
        ];


        $this->falseLabels = $falseLabels ?? [
            "default" => "False",
        ];
    }

    public function getAllTranslationsForString(string $translateString): array
    {
        switch (strtolower($translateString)) {
            case 'row':
                return $this->rowLabels;

            case 'positive':
                return $this->positiveLabels;

            case 'text':
                return $this->textLabels;

            case 'true':
                return $this->trueLabels;

            case 'false':
                return $this->falseLabels;

            default:
                throw new \InvalidArgumentException("Could not find translation for {$translateString}");
        }
    }
}
