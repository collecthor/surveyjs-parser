<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\FlattenResponseInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableSetInterface;

final readonly class FlattenResponseHelper implements FlattenResponseInterface
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
                $value = $variable->getValue($record);
                if (DataTypeHelper::valueIsNormal($value)) {
                    $flattened[$variable->getTitle($this->locale)] = $value->getDisplayValue($this->locale);
                }
            }
            yield $flattened;
        }
    }
}
