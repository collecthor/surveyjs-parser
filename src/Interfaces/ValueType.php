<?php

namespace Collecthor\SurveyjsParser\Interfaces;

enum ValueType
{
    /**
     * A value that is within the expected range
     */
    case Normal;
    /**
     * A value that is missing due to a system failure
     * "missing completely at random"
     * It is unrelated to other data in the response
     */
    case SystemMissing;
    /**
     * A value that is missing with the reason related to other observed variables
     * "missing at random"
     * This could be for example due to routing in the survey
     */
    case Missing;

    /**
     * The value falls outside the expected domain, imagine getting a text when expecting a number
     */
    case Invalid;
}
