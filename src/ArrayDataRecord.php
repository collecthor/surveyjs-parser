<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\SurveyjsParser\Interfaces\RecordInterface;

class ArrayDataRecord implements RecordInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }

    /**
     * @param non-empty-list<array-key> $path
     * @return string|int|float|bool|null|array<mixed>
     */
    public function getDataValue(array $path): string|int|float|bool|null|array
    {
        if (count($path) < 6) {
            /** @phpstan-ignore-next-line */
            return match (count($path)) {
                /** @phpstan-ignore-next-line */
                5 => $this->data[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]] ?? null,
                /** @phpstan-ignore-next-line */
                4 => $this->data[$path[0]][$path[1]][$path[2]][$path[3]] ?? null,
                /** @phpstan-ignore-next-line */
                3 => $this->data[$path[0]][$path[1]][$path[2]] ?? null,
                /** @phpstan-ignore-next-line */
                2 => $this->data[$path[0]][$path[1]] ?? null,
                /** @phpstan-ignore-next-line */
                1 => $this->data[$path[0]] ?? null
            } ?? null;
        }
        $data = $this->data;
        while (count($path) > 0 && is_array($data)) {
            $key = array_shift($path);
            $data = $data[$key] ?? null;
        }
        /** @phpstan-ignore-next-line */
        return $data;
    }

    public function allData(): array
    {
        return $this->data;
    }
}
