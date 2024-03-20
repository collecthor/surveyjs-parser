<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\SurveyjsParser\Interfaces\DateTimeValueInterface;

final readonly class DateTimeValue implements DateTimeValueInterface
{
    public function __construct(
        private \DateTimeInterface $value,
        private string $format
    ) {
    }
    public function getDisplayValue(?string $locale = null): string
    {
        return $this->value->format($this->format);
    }

    public function getValue(): \DateTimeInterface
    {
        return $this->value;
    }
}
