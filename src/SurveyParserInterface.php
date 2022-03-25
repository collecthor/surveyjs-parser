<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser;

use Collecthor\DataInterfaces\VariableInterface;
use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\Parsers\CallbackElementParser;
use Collecthor\SurveyjsParser\Parsers\DummyParser;
use Collecthor\SurveyjsParser\Parsers\PanelParser;
use Collecthor\SurveyjsParser\Parsers\SingleChoiceQuestionParser;
use Collecthor\SurveyjsParser\Parsers\TextQuestionParser;

interface SurveyParserInterface
{
    /**
     * Sets a parser for a question type. Will override the previously configured or default parser.
     */
    public function setParser(string $type, ElementParserInterface $parser): void;

    /**
     * Sets the default parser
     */
    public function setDefaultParser(ElementParserInterface $parser): void;

    /**
     * @param array<string, mixed> $structure
     */
    public function parseSurveyStructure(array $structure): VariableSetInterface;

    public function parseJson(string $json): VariableSetInterface;
}
