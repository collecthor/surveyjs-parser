<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class ArrayNode extends Node
{
    /**
     * @var Node[]
     */
    private array $nodes;
    public function __construct(Node ...$nodes)
    {
        $this->nodes = $nodes;
    }


    public function print(): string
    {
        return implode(', ', array_map(fn (Node $node) => $node->print(), $this->nodes));
    }
}
