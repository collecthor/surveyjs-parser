<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\ValueOptionInterface;

class StringValueOption implements ValueOptionInterface
{
    /**
     * @param array<string, string> $displayValues
     */
    public function __construct(
        private string $rawValue,
        private array $displayValues
    ) {
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getDisplayValue(?string $locale = null): string
    {
        return $this->displayValues[$locale] ?? $this->displayValues['default'] ?? $this->displayValues[array_keys($this->displayValues)[0]] ?? $this->rawValue;
    }
}
