<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface VariableTitleFormatterInterface
{
    public function formatHeaderText(VariableInterface $variable, string|null $locale): string;
}
