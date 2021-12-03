<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\DataInterfaces\ValueInterface;
use PHPUnit\Framework\Assert;

/**
 * @method iterable<scalar> getValidSamples()
 */
trait SimpleValueTest
{
    private function getSubject(mixed $param = null): ValueInterface
    {
        $reflection = new \ReflectionClass($this);

        /** @var non-empty-list<mixed> $sample */
        foreach ($this->getValidSamples() as $sample) {
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
            if ($result instanceof ValueInterface) {
                return $result;
            }
        }
        throw new \Exception('Add a covers class attribute');
    }

    /**
     * @return iterable<mixed>
     */
    abstract protected function getValidSamples(): iterable;
    /**
     * @return iterable<mixed>
     */
    abstract protected function getInvalidSamples(): iterable;

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
        $this->expectError();
        $this->getSubject($param);
    }

    public function testConstructorArgumentType(): void
    {
        $subject = $this->getSubject();

        $reflector = new \ReflectionClass($subject);

        /** @var \ReflectionMethod $constructor */
        $constructor = $reflector->getConstructor();
        Assert::assertInstanceOf(\ReflectionMethod::class, $constructor);
        $getterType = $reflector->getMethod('getRawValue')->getReturnType();

        Assert::assertGreaterThanOrEqual(1, $constructor->getNumberOfParameters());

        $valueType = $constructor->getParameters()[0]->getType();

        Assert::assertSame((string)$getterType, (string)$valueType);
    }
}
