<?php
declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

use Collecthor\DataInterfaces\ValueOptionInterface;

trait GetDisplayValue
{
    public function getDisplayValue(?string $locale = null): string
    {
        if (!empty($this->displayValues)) {
            $firstLabel = $this->displayValues[array_keys($this->displayValues)[0]];
        }

        return $this->displayValues[$locale] ?? $this->displayValues[ValueOptionInterface::DEFAULT_LOCALE] ?? $firstLabel ?? (string) $this->rawValue;
    }
}
