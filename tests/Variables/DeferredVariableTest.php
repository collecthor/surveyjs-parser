<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 *
 * @covers \Collecthor\SurveyjsParser\Variables\DeferredVariable
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\ResolvableVariableSet
 */
final class DeferredVariableTest extends TestCase
{
    public function testCreateDeferred(): void
    {
        $createVariable = function (ResolvableVariableSet $variables): VariableInterface {
            return new OpenTextVariable('var1', ['default' => 'var1'], ['var1'], );
        };

        $deferred = new DeferredVariable('var1', $createVariable);

        self::assertSame('var1', $deferred->getName());

        $variables = new ResolvableVariableSet();

        self::assertInstanceOf(OpenTextVariable::class, $deferred->resolve($variables));
    }

    public function testCallbackReturnType(): void
    {
        $createVariable = function (ResolvableVariableSet $variables): string {
            return "var1";
        };

        self::expectException(InvalidArgumentException::class);

        $deferred = new DeferredVariable('var1', $createVariable);
    }

    public function testCallbackArgumentType(): void
    {
        $createVariable = function (array $variables): VariableInterface {
            return new OpenTextVariable('var1', ['default' => 'var1'], ['var1']);
        };

        self::expectException(InvalidArgumentException::class);

        $deferred = new DeferredVariable('var1', $createVariable);
    }
}
