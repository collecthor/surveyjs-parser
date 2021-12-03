<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

/**
 * All class properties will be readonly in PHP8.1
 */
class SurveyConfiguration
{
    public string $commentPostfix = '-Comment';

    /**
     * @var non-empty-list<string>
     */
    public array $locales = ['default'];
}
