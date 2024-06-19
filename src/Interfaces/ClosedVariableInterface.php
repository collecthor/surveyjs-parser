<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface ClosedVariableInterface extends VariableInterface
{
    /**
     * @return list<ValueOptionInterface>
     */
    public function getOptions(): array;

    public function getNumberOfOptions(): int;
}
