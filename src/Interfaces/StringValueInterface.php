<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface StringValueInterface extends RawValueInterface
{
    public function getValue(): string;
}
