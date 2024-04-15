<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

/**
 * Survey wide configuration settings that affect parsing of individual questions
 * @codeCoverageIgnore
 */
final readonly class SurveyConfiguration
{
    public function __construct(
        public string $commentSuffix = '-Comment',
    ) {
    }
}
