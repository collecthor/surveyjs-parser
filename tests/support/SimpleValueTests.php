<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\SurveyjsParser\Interfaces\RawValueInterface;
use PHPUnit\Framework\Assert;

trait SimpleValueTests
{
    private function getSubject(mixed $param = null): RawValueInterface
    {
        $reflection = new \ReflectionClass($this);

        /** @var non-empty-list<mixed> $sample */
        foreach (static::getValidSamples() as $sample) {
            $defaultValue = $sample[0];
            break;
        }
        if (!isset($defaultValue)) {
            throw new \Exception('No valid sample was defined');
        }
        foreach ($reflection->getAttributes(CoversClass::class) as $attribute) {
            /** @var CoversClass $coversClass */
            $coversClass = $attribute->newInstance();
            $result = new ($coversClass->className)($param ?? $defaultValue);
            if ($result instanceof RawValueInterface) {
                return $result;
            }
        }
        throw new \Exception('Add a covers class attribute');
    }

    /**
     * @return iterable<mixed>
     */
    abstract protected static function getValidSamples(): iterable;
    /**
     * @return iterable<mixed>
     */
    abstract protected static function getInvalidSamples(): iterable;

    /**
     * @dataProvider getValidSamples
     */
    public function testValidSamples(mixed $param): void
    {
        $value = $this->getSubject($param);
        Assert::assertSame($param, $value->getRawValue());
    }

    /**
     * @dataProvider getInvalidSamples
     */
    public function testInvalidSamples(mixed $param): void
    {
        $this->expectException(\Throwable::class);
        $this->getSubject($param);
    }

    public function testConstructorArgumentType(): void
    {
        $subject = $this->getSubject();

        $reflector = new \ReflectionClass($subject);

        $constructor = $reflector->getConstructor();
        Assert::assertInstanceOf(\ReflectionMethod::class, $constructor);
        $getterType = $reflector->getMethod('getRawValue')->getReturnType();

        Assert::assertGreaterThanOrEqual(1, $constructor->getNumberOfParameters());

        $valueType = $constructor->getParameters()[0]->getType();

        Assert::assertSame((string)$getterType, (string)$valueType);
    }
}
