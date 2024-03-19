<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface SpecialValueInterface extends BaseValueInterface
{
    public function getType(): ValueType;
}
