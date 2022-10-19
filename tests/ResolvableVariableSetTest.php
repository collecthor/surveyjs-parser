<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use Collecthor\SurveyjsParser\Variables\DeferredVariable;
use Collecthor\SurveyjsParser\Variables\NumericVariable;
use Collecthor\SurveyjsParser\Variables\OpenTextVariable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\ResolvableVariableSet
 * @uses \Collecthor\SurveyjsParser\Traits\GetName
 * @uses \Collecthor\SurveyjsParser\Traits\GetTitle
 * @uses \Collecthor\SurveyjsParser\Variables\NumericVariable
 * @uses \Collecthor\SurveyjsParser\Variables\OpenTextVariable
 * @uses \Collecthor\SurveyjsParser\Variables\DeferredVariable
 */
final class ResolvableVariableSetTest extends TestCase
{
    public function testGetVariables(): void
    {
        $variables = [
            new OpenTextVariable('test1', ['default' => 'Title test 1'], ['test1']),
            new NumericVariable('test2', ['default' => 'Title test 2'], ['test2']),
        ];
        $resolvable = new ResolvableVariableSet(...$variables);

        self::assertSame('test1', $resolvable->getVariable('test1')->getName());
        self::assertSame('Title test 1', $resolvable->getVariable('test1')->getTitle());

        self::assertSame('test2', $resolvable->getVariable('test2')->getName());
        self::assertSame('Title test 2', $resolvable->getVariable('test2')->getTitle());

        self::expectException(InvalidArgumentException::class);
        $resolvable->getVariable('test3');
    }

    public function testResolveVariable(): void
    {
        $variableResolved = false;

        $callback = function (ResolvableVariableSet $set) use (&$variableResolved): VariableInterface {
            $variableResolved = true;
            return new OpenTextVariable('testresolved', ['default' => 'Test Resolved'], ['testresolved']);
        };

        $variables = [
            new NumericVariable('test', ['default' => 'Test'], ['test']),
            new DeferredVariable('test2', $callback),
        ];

        $resolvable = new ResolvableVariableSet(...$variables);

        self::assertFalse($variableResolved);

        $test2 = $resolvable->getVariable('test2');
        self::assertInstanceOf(OpenTextVariable::class, $test2);
        self::assertSame('testresolved', $test2->getName());
    }

    public function testResolveMultiLevel(): void
    {
        $topLevelResolved = false;

        $callback1 = function (ResolvableVariableSet $variables) use (&$topLevelResolved): VariableInterface {
            $result = $variables->getVariable('variable2');
            // The next line turns true when callback2 resolves, but phpstan does not understand this.
            /* @phpstan-ignore-next-line */
            self::assertTrue($topLevelResolved);
            return $result;
        };

        $callback2 = function (ResolvableVariableSet $variables) use (&$topLevelResolved): VariableInterface {
            $topLevelResolved = true;
            return $variables->getVariable('toplevel');
        };
        $variables = [
            new DeferredVariable('variable1', $callback1),
            new DeferredVariable('variable2', $callback2),
            new OpenTextVariable('toplevel', ['default' => 'toplevel'], ['toplevel']),
        ];

        $resolvable = new ResolvableVariableSet(...$variables);

        $variable1 = $resolvable->getVariable('variable1');

        self::assertTrue($topLevelResolved);
        self::assertInstanceOf(OpenTextVariable::class, $variable1);
        self::assertSame('toplevel', $variable1->getName());
    }
}
