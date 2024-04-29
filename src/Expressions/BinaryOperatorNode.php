<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

readonly class BinaryOperatorNode extends Node
{
    public function __construct(
        public Operator $operator,
        public Node $left,
        public Node $right
    ) {
    }

    public function print(): string
    {
        return "{$this->operator->name}({$this->left->print()}, {$this->right->print()})";
    }
}
