<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;

trait GetDisplayValue
{
    public function getDisplayValue(?string $locale = null): string
    {
        return $this->displayValues[$locale]
            ?? $this->displayValues[ValueOptionInterface::DEFAULT_LOCALE]
            ?? array_values($this->displayValues)[0]
            ?? (string) $this->getValue();
    }

    /**
     * @return array<string, string>
     */
    public function getDisplayValues(): array
    {
        return $this->displayValues;
    }
}
