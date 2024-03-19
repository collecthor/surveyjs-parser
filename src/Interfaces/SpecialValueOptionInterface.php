<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface SpecialValueOptionInterface extends SpecialValueInterface, ValueOptionInterface
{
    public function getValue(): string;
}
