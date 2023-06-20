<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface RecordInterface
{
    /**
     * Retrieve a value given by path to the data.
     * @phpstan-param non-empty-list<string> $path
     * @return string|int|float|bool|null|array<mixed>
     */
    public function getDataValue(array $path): string|int|float|bool|null|array;

    /**
     * Return the data in the record
     * @return array<string, mixed>
     */
    public function allData(): array;
}
