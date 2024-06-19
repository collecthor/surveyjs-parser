<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

enum Measure
{
    case Nominal;
    case Ordinal;
    case Scale;
}
