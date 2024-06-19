<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

trait GetName
{
    private readonly string $name;

    public function getName(): string
    {
        return $this->name;
    }
}
