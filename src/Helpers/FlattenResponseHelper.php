<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Helpers;

use Collecthor\SurveyjsParser\FlattenResponseInterface;
use Collecthor\SurveyjsParser\Interfaces\RecordInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableTitleFormatterInterface;
use function iter\toArray;

final readonly class FlattenResponseHelper implements FlattenResponseInterface
{
    /**
     * @var \SplObjectStorage<VariableInterface, string>
     */
    private \SplObjectStorage $titleMap;

    /**
     * @var list<VariableInterface>
     */
    private array $variables;

    public function __construct(
        VariableSetInterface $variables,
        private ?string $locale = null,
        VariableTitleFormatterInterface|null $formatter = null
    ) {
        $this->variables = toArray($variables->getVariables());
        $formatter ??= new class() implements VariableTitleFormatterInterface {
            public function formatHeaderText(VariableInterface $variable, string|null $locale): string
            {
                return $variable->getTitle($locale);
            }
        };
        $this->titleMap = new \SplObjectStorage();
        foreach ($this->variables as $variable) {
            $this->titleMap->offsetSet($variable, $formatter->formatHeaderText($variable, $locale));
        }
    }

    public function flatten(RecordInterface $record): array
    {
        $flattened = [];
        foreach ($this->variables as $variable) {
            $baseTitle = $this->titleMap->offsetGet($variable);
            // Check if it is a multiple choice variable.
            if (DataTypeHelper::isMultipleChoice($variable)) {
                $value = $variable->getValue($record);
                if ($variable->isOrdered()) {
                    // This is a ranking question, we export a column based on rank.
                    for ($i = 1; $i <= $variable->getNumberOfOptions(); $i++) {
                        $title = "{$baseTitle} ({$i})";
                        if (DataTypeHelper::valueIsNormal($value)) {
                            $flattened[$title] = $value->getIndex($i - 1)?->getDisplayValue($this->locale);
                        } else {
                            $displayValue = $value->getDisplayValue($this->locale);
                            $flattened[$title] = $displayValue;
                        }
                    }
                } else {
                    // This is a multiple choice question, we export a column for each normal option
                    foreach ($variable->getOptions() as $option) {
                        if (!DataTypeHelper::valueIsNormal($option)) {
                            continue;
                        }
                        $title = "{$baseTitle} - {$option->getDisplayValue($this->locale)}";
                        if (DataTypeHelper::valueIsNormal($value)) {
                            $flattened[$title] = $value->contains($option) ? 1 : 0;
                        } else {
                            $flattened[$title] = $value->getDisplayValue($this->locale);
                        }
                    }
                }
            } else {
                $value = $variable->getValue($record);
                $flattened[$baseTitle] = $value->getDisplayValue($this->locale);
            }
        }
        return $flattened;
    }

    public function flattenAll(iterable $records): iterable
    {
        foreach ($records as $record) {
            yield $this->flatten($record);
        }
    }
}
