<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Values;

/**
 * This is a value object for an anonymous javascript function.
 * The way to use it is something like this:
 * ```php
 * $function = new JavascriptFunction("() => true");
 * $js = <<<JS
 *     const func = {$javascriptFunction};
 * JS;
 * ```
 *
 */
final readonly class JavascriptFunction implements \Stringable
{
    private string $definition;
    public function __construct(string $definition)
    {
        $this->definition = trim($definition);
    }

    public function __toString(): string
    {
        return $this->definition;
    }
}
