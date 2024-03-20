<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface DateTimeValueInterface extends BaseValueInterface
{
    public function getValue(): \DateTimeInterface;
}
