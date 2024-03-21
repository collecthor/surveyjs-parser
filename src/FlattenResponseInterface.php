<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\SurveyjsParser\Interfaces\RecordInterface;

interface FlattenResponseInterface
{
    /**
     * Convert all records to a flat array
     * @param iterable<RecordInterface> $records
     * @return iterable<array<string, string|int|null>>
     */
    public function flattenAll(iterable $records): iterable;
}
