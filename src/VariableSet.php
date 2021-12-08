<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use InvalidArgumentException;

class VariableSet implements VariableSetInterface
{
    /**
     * @var array<string, VariableInterface>
     */
    private array $variables = [];


    public function __construct(VariableInterface ...$variables)
    {
        foreach ($variables as $variable) {
            $this->variables[$variable->getName()] = $variable;
        }
    }

    public function getVariableNames(): iterable
    {
        foreach ($this->variables as $key => $dummy) {
            yield $key;
        }
    }

    public function getVariable(string $name): VariableInterface
    {
        if (!isset($this->variables[$name])) {
            throw new InvalidArgumentException("Unknown variable name: $name");
        }
        return $this->variables[$name];
    }

    public function getVariables(): iterable
    {
        foreach ($this->variables as $variable) {
            yield $variable;
        };
    }
}
