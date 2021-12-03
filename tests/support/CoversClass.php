<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CoversClass
{
    /**
     * @param class-string $className
     */
    public function __construct(public readonly string $className)
    {
    }
}
