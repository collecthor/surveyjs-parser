<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

use Collecthor\DataInterfaces\ValueOptionInterface;

trait GetTitle
{
    /**
     * @var array<string, string>
     */
    private array $titles;

    public function getTitle(?string $locale = null): string
    {
        return $this->titles[$locale] ?? $this->titles[ValueOptionInterface::DEFAULT_LOCALE] ?? $this->titles[array_keys($this->titles)[0]] ?? "No title";
    }
}
