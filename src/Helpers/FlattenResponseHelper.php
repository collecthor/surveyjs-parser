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
                    $value = $variable->getValue($record);
                    if ($variable->isOrdered()) {
                        // This is a ranking question, we export a column based on rank.
                        if (DataTypeHelper::valueIsNormal($value)) {
                            $selectedOptions = $value->getValue();
                            for ($i = 1; $i <= count($variable->getOptions()); $i++) {
                                $title = "{$baseTitle} ({$i})";
                                $flattened[$title] = isset($selectedOptions[$i - 1]) ? $selectedOptions[$i - 1]->getDisplayValue($this->locale) : null;
                            }
                        } else {
                            $displayValue = $value->getDisplayValue($this->locale);
                            for ($i = 1; $i <= count($variable->getOptions()); $i++) {
                                $title = "{$baseTitle} ({$i})";
                                $flattened[$title] = $displayValue;
                            }
                        }
                    } else {
                        // This is a multiple choice question, we export a column for each option
                        foreach ($variable->getOptions() as $option) {
                            if (!DataTypeHelper::valueIsNormal($option)) {
                                continue;
                            }
                            $title = "{$baseTitle} - {$option->getDisplayValue($this->locale)}";
                            if (DataTypeHelper::valueIsNormal($value)) {
                                $flattened[$title] = in_array($option, $value->getValue(), true) ? 1 : 0;
                            } else {
                                $flattened[$title] = $value->getDisplayValue($this->locale);
                            }
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
