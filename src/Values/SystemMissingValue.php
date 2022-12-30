<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

use Collecthor\DataInterfaces\MissingValueInterface;

final class SystemMissingValue implements MissingValueInterface
{
    public function isSystemMissing(): bool
    {
        return true;
    }

    public function getRawValue(): null|string
    {
        return null;
    }
}
