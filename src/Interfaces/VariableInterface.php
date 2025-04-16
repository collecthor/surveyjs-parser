<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Interfaces;

use Collecthor\SurveyjsParser\Values\JavascriptFunction;

/**
 * @internal
 */
interface VariableInterface
{
    /**
     * @return string A unique identifier for the variable
     */
    public function getName(): string;

    /**
     * @return string The title text in the default language
     * If a locale is given the display value should be returned using the given locale. If the locale is not available
     * a fallback MUST be used and an exception MUST NOT be thrown
     */
    public function getTitle(null|string $locale = null): string;

    public function getValue(RecordInterface $record): BaseValueInterface;

    /**
     * Return the type of measure for this variable
     */
    public function getMeasure(): Measure;

    public function getRawConfigurationValue(string|int $key): mixed;

    /**
     * Return a javascript function that takes a data dictionary and extracts the RAW value of the variable.
     * @return JavascriptFunction
     */
    public function getExtractor(): JavascriptFunction;
}
