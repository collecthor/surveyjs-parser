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
                // Check if it is a multiple choice variable.
                if (DataTypeHelper::isMultipleChoice($variable)) {
                    $baseTitle = $variable->getTitle($this->locale);
                    $selectedOptions = $variable->getValue($record);
                    foreach ($variable->getOptions() as $option) {
                        if (!DataTypeHelper::valueIsNormal($option)) {
                            continue;
                        }
                        $title = "{$baseTitle} - {$option->getDisplayValue($this->locale)}";
                        if (DataTypeHelper::valueIsNormal($selectedOptions)) {
                            $flattened[$title] = in_array($option, $selectedOptions->getValue(), true) ? 1 : 0;
                        } else {
                            $flattened[$title] = $selectedOptions->getDisplayValue($this->locale);
                        }
                    }
                } else {
                    $value = $variable->getValue($record);
                    if (DataTypeHelper::valueIsNormal($value)) {
                        $flattened[$variable->getTitle($this->locale)] = $value->getDisplayValue($this->locale);
                    }
                }
            }
            yield $flattened;
        }
    }
}
