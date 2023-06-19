<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Closure;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionNamedType;

/**
 * @internal
 */
final class DeferredVariable
{
    /**
     * @param string $name
     * @param Closure(ResolvableVariableSet $variables): VariableInterface $closure
     */
    public function __construct(private readonly string $name, private readonly Closure $closure)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function resolve(ResolvableVariableSet $variables): VariableInterface
    {
        return ($this->closure)($variables);
    }
}
