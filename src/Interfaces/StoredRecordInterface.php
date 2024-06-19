<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

use DateTimeInterface;

interface StoredRecordInterface extends RecordInterface
{
    /**
     * Return the unique ID of this record
     */
    public function getRecordId(): int;

    /**
     * Since there is no interface for immutable date times, implementations MUST return a fresh object every time.
     */
    public function getStarted(): DateTimeInterface;

    /**
     * Since there is no interface for immutable date times, implementations MUST return a fresh object every time.
     */
    public function getLastUpdate(): DateTimeInterface;
}
