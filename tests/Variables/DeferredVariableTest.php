<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeferredVariable::class)]
final class DeferredVariableTest extends TestCase
{
    public function testCreateDeferred(): void
    {
        $result = new OpenTextVariable('var1', dataPath: ['var1'], titles: ['default' => 'var1']);
        $createVariable = static fn (ResolvableVariableSet $variables): VariableInterface => $result;


        $variables = new ResolvableVariableSet();

        $deferred = new DeferredVariable('var1', $createVariable);

        self::assertSame('var1', $deferred->getName());

        self::assertSame($result, $deferred->resolve($variables));
    }
}
