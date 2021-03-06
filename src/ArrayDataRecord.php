<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\RecordInterface;

class ArrayDataRecord implements RecordInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private readonly array $data)
    {
    }


    public function getDataValue(array $path): string|int|float|null|array
    {
        $data = $this->data;

        while (count($path) > 0 && is_array($data)) {
            $key = array_shift($path);
            $data = $data[$key] ?? null;
        }
        return $data;
    }

    public function allData(): array
    {
        return $this->data;
    }
}
