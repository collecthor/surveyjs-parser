<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\RecordInterface;

interface FlattenResponseInterface
{
    /**
     * Convert all records to a flat array
     * @param iterable<RecordInterface> $records
     * @return iterable<array<string, string>>
     */
    public function flattenAll(iterable $records): iterable;
}
