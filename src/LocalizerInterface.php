<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

interface LocalizerInterface
{
    /**
     *
     * @param string $translateString
     * @return array<string, string>
     */
    public function getAllTranslationsForString(string $translateString): array;
}
