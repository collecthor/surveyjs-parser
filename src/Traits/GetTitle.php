<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;

trait GetTitle
{
    /**
     * @var array<string, string>
     */
    private readonly array $titles;

    public function getTitle(?string $locale = null): string
    {
        return $this->titles[$locale] ?? $this->titles[ValueOptionInterface::DEFAULT_LOCALE] ?? $this->titles[array_keys($this->titles)[0] ?? 'default'] ?? "No title";
    }
}
