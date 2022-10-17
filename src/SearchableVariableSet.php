<?php

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use InvalidArgumentException;

final class SearchableVariableSet 
{
    /**
     * @var array<string, VariableInterface|DeferredVariable>
     */
    private array $variables = [];

    public function __construct(VariableInterface | DeferredVariable ...$variables)
    {
        foreach ($variables as $variable) {
            $this->variables[$variable->getName()] = $variable;
        }
    }

    public function getVariable(string $name): VariableInterface { 
        if (!isset($this->variables[$name])) {
            throw new InvalidArgumentException("Unknown variable name: $name");
        }
        $variable = $this->variables[$name];

        if ($variable instanceof DeferredVariable) {
            return $variable->resolve($this);
        } else {
            return $variable;
        }
    }

}