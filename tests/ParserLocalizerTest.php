<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Tests;

use Collecthor\SurveyjsParser\ParserLocalizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Collecthor\SurveyjsParser\ParserLocalizer
 */
final class ParserLocalizerTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $localizer = new ParserLocalizer();
        
        self::assertSame('Row', $localizer->getAllTranslationsForString('Row')['default']);
        self::assertSame('Positive', $localizer->getAllTranslationsForString('Positive')['default']);
        self::assertSame('Text', $localizer->getAllTranslationsForString('Text')['default']);
        self::assertSame('True', $localizer->getAllTranslationsForString('True')['default']);
        self::assertSame('False', $localizer->getAllTranslationsForString('False')['default']);
    }

    public function testSingleCustomValue(): void
    {
        $localizer = new ParserLocalizer(trueLabels: ['default' => 'True test']);

        self::assertSame('Row', $localizer->getAllTranslationsForString('Row')['default']);
        self::assertSame('Positive', $localizer->getAllTranslationsForString('Positive')['default']);
        self::assertSame('Text', $localizer->getAllTranslationsForString('Text')['default']);
        self::assertSame('True test', $localizer->getAllTranslationsForString('True')['default']);
        self::assertSame('False', $localizer->getAllTranslationsForString('False')['default']);
    }

    public function testMultipleLanguages(): void
    {
        $localizer = new ParserLocalizer(trueLabels: ['default' => 'True', 'nl' => 'Waar']);

        self::assertSame('Row', $localizer->getAllTranslationsForString('Row')['default']);
        self::assertSame('Positive', $localizer->getAllTranslationsForString('Positive')['default']);
        self::assertSame('Text', $localizer->getAllTranslationsForString('Text')['default']);
        self::assertSame('True', $localizer->getAllTranslationsForString('True')['default']);
        self::assertSame('False', $localizer->getAllTranslationsForString('False')['default']);
        self::assertSame('Waar', $localizer->getAllTranslationsForString('True')['nl']);
    }
}
