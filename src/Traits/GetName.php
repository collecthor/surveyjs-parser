<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Traits;

trait GetName
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }
}
