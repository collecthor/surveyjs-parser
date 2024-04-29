<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class VariableNode extends Node
{
    /**
     * @var list<string>
     */
    public array $index;
    public function __construct(public string $name, string ...$index)
    {
        $this->index = array_values($index);
    }


    public function print(): string
    {
        if ($this->index === []) {
            return "Variable({$this->name})";
        }
        return "Variable({$this->name}, " . implode(", ", $this->index) . ')';
    }
}
