<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\Variables;

use Collecthor\DataInterfaces\JavascriptVariableInterface;
use Collecthor\DataInterfaces\ValueOptionInterface;
use Collecthor\DataInterfaces\VariableInterface;
use PHPUnit\Framework\TestCase;

abstract class VariableTest extends TestCase
{
    /**
     * @param array<string, mixed> $rawConfiguration
     * @return VariableInterface
     */
    abstract protected function getVariableWithRawConfiguration(array $rawConfiguration): VariableInterface;
    abstract protected function getVariableWithName(string $name): JavascriptVariableInterface;
    /**
     * @param array<string, string> $titles
     */
    abstract protected function getVariableWithTitles(array $titles): VariableInterface;

    /**
     * @return iterable<list<array<string, mixed>>>
     */
    public function rawConfigurationProvider(): iterable
    {
        yield [
            [
                'value' => 154
            ]
        ];
    }

    /**
     * @dataProvider rawConfigurationProvider
     * @param array<string,mixed> $rawConfiguration
     */
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

    final public function testGetJavascriptRepresentation(): void
    {
        $variable = $this->getVariableWithName('test');
        $js = $variable->getJavascriptRepresentation();
        self::assertNotEmpty($js);
        self::markTestIncomplete("We should implement running the javascript to see if it actually works");
    }
}
