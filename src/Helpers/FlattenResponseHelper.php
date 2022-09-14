<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\FlattenResponseInterface;

final class FlattenResponseHelper implements FlattenResponseInterface
{
    public function __construct(private VariableSetInterface $variables, private ?string $locale = null)
    {
    }

    /** @param iterable<RecordInterface> $records */
    public function flattenAll(iterable $records): iterable
    {
        foreach ($records as $record) {
            $flattened = [];
            foreach ($this->variables->getVariables() as $variable) {
                $flattened[$variable->getTitle($this->locale)] = $variable->getDisplayValue($record, $this->locale)->getRawValue();
            }
            yield $flattened;
        }
    }
}
