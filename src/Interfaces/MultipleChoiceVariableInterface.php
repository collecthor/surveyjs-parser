<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface MultipleChoiceVariableInterface extends ClosedVariableInterface
{
    /**
     * This is used to identify ranking questions; they contain the same data as other multiple choice questions
     * but the order of the values is relevant
     * @return bool whether the value ordering is relevant.
     */
    public function isOrdered(): bool;
}
