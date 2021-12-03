<?php

declare(strict_types=1);
namespace Collecthor\SurveyjsParser\Parsers;

use Collecthor\DataInterfaces\VariableSetInterface;
use Collecthor\SurveyjsParser\ElementParserInterface;
use Collecthor\SurveyjsParser\SurveyConfiguration;

class CallbackElementParser implements ElementParserInterface
{
    public function __construct(private \Closure $callback)
    {
    }

    public function parse(ElementParserInterface $parent, array $questionConfig, SurveyConfiguration $surveyConfiguration, array $dataPrefix = []): iterable
    {
        yield from ($this->callback)($parent, $questionConfig, $surveyConfiguration, $dataPrefix);
    }
}
