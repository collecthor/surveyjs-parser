<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

interface RawValueInterface
{
    /**
     * Returns a raw value as obtained from the record.
     * The return type includes \DateTimeInterface since a raw value could be a computed or system generated value
     * and therefore have a type
     * @return string|int|float|bool|array<mixed>|null|\DateTimeInterface
     */
    public function getRawValue(): string|int|float|bool|null|array|\DateTimeInterface;

    /**
     * @return string|int|float|bool|array<mixed>|\DateTimeInterface|null
     */
    public function getValue(): string|int|float|bool|null|array|\DateTimeInterface;


    public function getType(): ValueType;

    public function getDisplayValue(string|null $locale = null): string;
}
