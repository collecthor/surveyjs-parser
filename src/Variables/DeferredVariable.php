<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Closure;
use Collecthor\SurveyjsParser\Interfaces\VariableInterface;
use Collecthor\SurveyjsParser\ResolvableVariableSet;

/**
 * @internal
 */
final readonly class DeferredVariable
{
    /**
     * @param string $name
     * @param Closure(ResolvableVariableSet $variables): VariableInterface $closure
     */
    public function __construct(private string $name, private Closure $closure)
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
