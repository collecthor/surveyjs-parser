<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests\support;

use Collecthor\DataInterfaces\RecordInterface;
use Collecthor\DataInterfaces\ValueInterface;
use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\SurveyConfiguration;
use PHPUnit\Framework\TestCase;
use function iter\toArray;

/**
 * @mixin TestCase
 */
trait ValueNameTests
{
    abstract protected function getParser(): ElementParserInterface;

    final public function testMissingValueName(): void
    {
        $parser = $this->getParser();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The valueName or name key must be set");
        toArray($parser->parse(new DummyParser(), [
            'valueName' => null,
            'name' => null
        ], new SurveyConfiguration()));
    }

    final public function testValueNameTakesPrio(): void
    {
        $parser = $this->getParser();
        /** @var VariableInterface[] $variables */
        $variables = toArray($parser->parse(new DummyParser(), [
            'valueName' => 'valueName',
            'name' => 'name',
            'choices' => [
                'a'
            ]
        ], new SurveyConfiguration()));
        self::assertCount(1, $variables);
        $record = $this->getMockBuilder(RecordInterface::class)->getMock();
        $record->expects(self::once())->method('getDataValue')->with(['valueName'])->willReturn("a");
        $value = $variables[0]->getValue($record);
        self::assertInstanceOf(ValueInterface::class, $value);
        self::assertSame("a", $value->getRawValue());
    }
}
