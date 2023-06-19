<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\FlattenResponseInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableSetInterface;

final class FlattenResponseHelper implements FlattenResponseInterface
{
    public function __construct(private readonly VariableSetInterface $variables, private readonly ?string $locale = null)
    {
    }

    /** @param iterable<RecordInterface> $records */
    public function flattenAll(iterable $records): iterable
    {
        foreach ($records as $record) {
            $flattened = [];
            foreach ($this->variables->getVariables() as $variable) {
                $flattened[$variable->getTitle($this->locale)] = $variable->getValue($record)->getDisplayValue($this->locale);
            }
            yield $flattened;
        }
    }
}
