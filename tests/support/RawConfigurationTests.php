<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait RawConfigurationTests
{
    abstract protected function getParser(): ElementParserInterface;
    /**
     * @return non-empty-list<non-empty-array<string, mixed>>
     */
    abstract protected function validConfigs(): array;

    public function testRawConfiguration(): void
    {
        $parser = $this->getParser();


        foreach ($this->validConfigs() as $rawConfiguration) {
            // Create random entries
            for ($i = 0; $i < 5; $i++) {
                $rawConfiguration[random_bytes(15)] = random_int(1, 10000);
            }
            // Test each resulting variable
            foreach ($parser->parse(new DummyParser(), $rawConfiguration, new SurveyConfiguration()) as $variable) {
                // Test each key
                foreach ($rawConfiguration as $key => $value) {
                    if ($variable instanceof VariableInterface) {
                        self::assertSame($value, $variable->getRawConfigurationValue($key));
                    }
                }
            }
        }
    }
}
