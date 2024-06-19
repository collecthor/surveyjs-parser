<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

final readonly class FunctionNode extends Node
{
    /**
     * @var Node[]
     */
    public array $arguments;

    public function __construct(public string $name, Node ...$arguments)
    {
        $this->arguments = $arguments;
    }


    public function print(): string
    {
        return "{$this->name}(" . implode(", ", array_map(fn (Node $node) => $node->print(), $this->arguments)) . ")";
    }
}
