<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\SurveyjsParser\Interfaces\ValueOptionInterface;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class VariableTestBase extends TestCase
{
    /**
     * @param array<string, mixed> $rawConfiguration
     * @return VariableInterface
     */
    abstract protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface;

    /**
     * @param non-empty-list<string> $dataPath
     */
    abstract protected function getVariableWithName(string $name, array $dataPath = ['path']): VariableInterface;
    /**
     * @param array<string, string> $titles
     */
    abstract protected function getVariableWithTitles(array $titles): VariableInterface;

    /**
     * @return iterable<list<array<string, mixed>>>
     */
    public static function rawConfigurationProvider(): iterable
    {
        yield [
            [
                'value' => 154
            ]
        ];
    }

    /**
     * @param array<string,mixed> $rawConfiguration
     */
    #[DataProvider('rawConfigurationProvider')]
    final public function testRawConfiguration(array $rawConfiguration): void
    {
        $variable = $this->getVariableWithRawConfiguration($rawConfiguration);
        foreach ($rawConfiguration as $key => $value) {
            self::assertSame($value, $variable->getRawConfigurationValue($key));
        }
        self::assertNull($variable->getRawConfigurationValue(random_bytes(5)));
    }

    final public function testGetName(): void
    {
        self::assertSame('test12345', $this->getVariableWithName('test12345')->getName());
    }

    final public function testGetTitle(): void
    {
        $titles = [
            ValueOptionInterface::DEFAULT_LOCALE => 'default title',
            'en' => 'English title'
        ];
        $variable = $this->getVariableWithTitles($titles);
        foreach ($titles as $locale => $title) {
            self::assertSame($title, $variable->getTitle($locale));
        }
        self::assertSame($titles[ValueOptionInterface::DEFAULT_LOCALE], $variable->getTitle());
    }

    final public function testGetExtractor(): void
    {
        $variable = $this->getVariableWithName('test12345', ['test', 'def']);
        self::assertSame('(data) => data["test"]?.["def"] ?? null', (string) $variable->getExtractor());
    }
}
