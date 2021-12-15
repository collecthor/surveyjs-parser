<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\VariableSet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\VariableSet
 */
class VariableSetTest extends TestCase
{
    private function createVariableWithName(string $name): VariableInterface
    {
        $variable = $this->getMockBuilder(VariableInterface::class)->getMock();
        $variable->expects(self::once())->method('getName')->willReturn($name);
        return $variable;
    }


    public function testVariableNames(): void
    {
        $variables = [];
        $names = [];
        for ($i = 0; $i < 10; $i++) {
            $name = random_bytes(15);
            $names[] = $name;
            $variables[] = $this->createVariableWithName($name);
        }

        $set = new VariableSet(...$variables);
        $i = 0;
        foreach ($set->getVariableNames() as $name) {
            self::assertSame($names[$i], $name);
            $i++;
        }
    }

    public function testGetVariables(): void
    {
        $variables = [];
        for ($i = 0; $i < 10; $i++) {
            $variables[] = $this->createVariableWithName(random_bytes(15));
        }

        $set = new VariableSet(...$variables);
        $i = 0;
        foreach ($set->getVariables() as $variable) {
            self::assertSame($variables[$i], $variable);
            $i++;
        }
    }

    public function testGetVariable(): void
    {
        $variables = [];
        for ($i = 0; $i < 10; $i++) {
            $name = random_bytes(15);
            $variables[$name] = $this->createVariableWithName($name);
        }

        $set = new VariableSet(...$variables);
        foreach ($set->getVariableNames() as $name) {
            self::assertSame($variables[$name], $set->getVariable($name));
        }
    }

    public function testUnknownVariable(): void
    {
        $set = new VariableSet();
        $this->expectException(\InvalidArgumentException::class);
        $set->getVariable('abcdef');
    }
}
