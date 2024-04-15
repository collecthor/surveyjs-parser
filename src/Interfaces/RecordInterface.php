<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface RecordInterface
{
    /**
     * Retrieve a value given by path to the data.
     * @param non-empty-list<string> $path
     */
    public function getDataValue(array $path): mixed;

    /**
     * Return the data in the record
     * @return array<string, mixed>
     */
    public function allData(): array;
}
