<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

use InvalidArgumentException;

interface VariableSetInterface
{
    /**
     * @return iterable<string> Return a list of variable IDs
     */
    public function getVariableNames(): iterable;

    /**
     * @throws InvalidArgumentException when the given name is not valid
     */
    public function getVariable(string $name): VariableInterface;

    /**
     * @return iterable<VariableInterface> Returns an iterator that will yield the VariableInterface objects
     */
    public function getVariables(): iterable;
}
