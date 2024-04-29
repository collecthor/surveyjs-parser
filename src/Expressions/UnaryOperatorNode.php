<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class UnaryOperatorNode extends Node
{
    public function __construct(
        public Operator $operator,
        public Node $node,
    ) {
    }

    public function print(): string
    {
        return "{$this->operator->name}({$this->node->print()})";
    }
}
