<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use InvalidArgumentException;

class VariableSet implements VariableSetInterface
{
    /**
     * @var VariableInterface[]
     */
    private array $variables = [];

    public function __construct(VariableInterface ...$variables)
    {
        $this->variables = array_values($variables);
    }

    public function getVariableNames(): iterable
    {
        foreach ($this->variables as $variable) {
            yield $variable->getName();
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
        yield from $this->variables;
    }
}
