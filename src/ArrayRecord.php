<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\RecordInterface;
use DateTimeInterface;

/**
 * A simple array wrapper that implements `RecordInterface`
 */
class ArrayRecord implements RecordInterface
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        private array $data,
        private int $id,
        private DateTimeInterface $started,
        private DateTimeInterface $lastUpdate
    ) {
    }

    public function getDataValue(array $path): string|int|float|null|array
    {
        $data = $this->data;

        while (!empty($path) && is_array($data)) {
            $key = array_shift($path);
            $data = $data[$key] ?? null;
        }
        return $data;
    }

    public function getRecordId(): int
    {
        return $this->id;
    }

    public function getStarted(): DateTimeInterface
    {
        return clone $this->started;
    }

    public function getLastUpdate(): DateTimeInterface
    {
        return clone $this->lastUpdate;
    }
}
