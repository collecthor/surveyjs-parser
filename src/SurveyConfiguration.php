<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

/**
 * Survey wide configuration settings that affect parsing of individual questions
 * @codeCoverageIgnore
 */
class SurveyConfiguration
{
    public function __construct(
        public readonly string $commentPostfix = '-Comment',
        public readonly bool $storeOthersAsComment = true,
    ) {
    }
}
