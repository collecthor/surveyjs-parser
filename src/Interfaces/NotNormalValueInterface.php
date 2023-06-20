<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface NotNormalValueInterface extends RawValueInterface
{
    public function getValue(): never;

    /**
     * @return ValueType::Invalid|ValueType::SystemMissing|ValueType::Missing
     */
    public function getType(): ValueType;

    public function getDisplayValue(string|null $locale = null): string;
}
