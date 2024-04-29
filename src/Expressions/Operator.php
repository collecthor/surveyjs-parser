<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Expressions;

enum Operator: string
{
    case Addition = '+';
    case Subtraction = '-';
    case Multiplication = '*';
    case Division = '/';

    case Lt = '<';

    case Gt = '>';

    case Eq = '=';
    case Eq2 = '==';
    case Or = 'or';
    case And = 'and';

    case AnyOf = 'anyof';

    case Contains = 'contains';
    case NotContains = 'notcontains';

    case NotEmpty = 'notempty';
    case Empty = 'empty';



    private function getPrecedence(): int
    {
        return match ($this) {
            Operator::Eq => 8,
            Operator::Or => 7,
            Operator::And => 6,
            Operator::AnyOf, Operator::Contains, Operator::NotContains => 8,
            Operator::Lt, Operator::Gt => 12,
            Operator::Addition, Operator::Subtraction => 90,
            Operator::Multiplication, Operator::Division => 100,
            default => 500,
        };
    }

    public function hasPrecendenceOver(Operator $operator): bool
    {
        return $this->getPrecedence() >= $operator->getPrecedence();
    }

    /**
     * @return list<string>;
     */
    public static function values(): array
    {
        return array_map(fn (Operator $op) => $op->value, self::cases());
    }

    public function isUnary(): bool
    {
        return match ($this) {
            Operator::NotEmpty, Operator::Empty => true,
            default => false
        };
    }
}
