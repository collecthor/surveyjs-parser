<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface RecordInterface
{
    /**
     * Retrieve a value given by path to the data.
     * @param non-empty-list<string> $path
     * @return string|int|float|bool|null|array<mixed>|\DateTimeImmutable
     */
    public function getDataValue(array $path): string|int|float|bool|null|array|\DateTimeImmutable;

    /**
     * Return the data in the record
     * @return array<string, mixed>
     */
    public function allData(): array;
}
