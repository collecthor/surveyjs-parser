<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface BooleanValueInterface extends BaseValueInterface, ValueOptionInterface
{
    public function getValue(): bool;
}
