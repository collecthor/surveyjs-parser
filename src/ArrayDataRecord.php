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
     * @param list<array-key> $path
     */
    public function getDataValue(array $path): mixed
    {
        if (count($path) < 6) {
            $result = match (count($path)) {
                5 => $this->data[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]] ?? null,
                4 => $this->data[$path[0]][$path[1]][$path[2]][$path[3]] ?? null,
                3 => $this->data[$path[0]][$path[1]][$path[2]] ?? null,
                2 => $this->data[$path[0]][$path[1]] ?? null,
                1 => $this->data[$path[0]] ?? null,
                0 => null
            } ?? null;
        } else {
            $result = $this->data;
            while (count($path) > 0 && is_array($result)) {
                $key = array_shift($path);
                $result = $result[$key] ?? null;
            }
        }
        return $result;
    }

    public function allData(): array
    {
        return $this->data;
    }
}
