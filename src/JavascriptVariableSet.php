<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\JavascriptVariableSetInterface;
use InvalidArgumentException;

class JavascriptVariableSet implements JavascriptVariableSetInterface
{
    /**
     * @var array<string, JavascriptVariableInterface>
     */
    private array $variables = [];


    public function __construct(JavascriptVariableInterface ...$variables)
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

    public function getVariable(string $name): JavascriptVariableInterface
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
