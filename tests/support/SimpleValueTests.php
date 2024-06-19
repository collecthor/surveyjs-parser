<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\SurveyjsParser\Interfaces\BaseValueInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

trait SimpleValueTests
{
    private function getSubject(mixed $param = null): BaseValueInterface
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

            $valueClass = $coversClass->className();
            if (method_exists($valueClass, 'create')) {
                $result = ($valueClass)::create($param ?? $defaultValue);
            } else {
                $result = new $valueClass($param ?? $defaultValue);
            }

            if ($result instanceof BaseValueInterface) {
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

    #[DataProvider('getValidSamples')]
    public function testValidSamples(mixed $param): void
    {
        $value = $this->getSubject($param);
        Assert::assertSame($param, $value->getValue());
    }

    #[DataProvider('getInvalidSamples')]
    public function testInvalidSamples(mixed $param): void
    {
        $this->expectException(\Throwable::class);
        $this->getSubject($param);
    }
}
