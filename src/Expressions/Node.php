<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

abstract readonly class Node
{
    abstract public function print(): string;
}
