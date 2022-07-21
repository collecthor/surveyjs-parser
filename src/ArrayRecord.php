<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\StoredRecordInterface;
use DateTimeInterface;

/**
 * A simple array wrapper that implements `RecordInterface`
 */
class ArrayRecord extends ArrayDataRecord implements StoredRecordInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        array $data,
        private readonly int $id,
        private readonly DateTimeInterface $started,
        private readonly DateTimeInterface $lastUpdate
    ) {
        parent::__construct($data);
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
