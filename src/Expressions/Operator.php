<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

enum Operator: string
{
    case Addition = '+';
    case Subtraction = '-';
    case Multiplication = '*';
    case Division = '/';



    private function getPrecedence(): int
    {
        return match ($this) {
            Operator::Addition, Operator::Subtraction => 0,
            Operator::Multiplication, Operator::Division => 1,
        };
    }

    public function hasPrecendenceOver(Operator $operator): bool
    {
        return $this->getPrecedence() >= $operator->getPrecedence();
    }
}
