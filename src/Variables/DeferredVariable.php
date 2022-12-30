<?php

declare(strict_types=1);

namespace Collecthor\SurveyjsParser\Variables;

use Closure;
use Collecthor\DataInterfaces\VariableInterface;
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
     * @var \Closure((ResolvableVariableSet $variables): VariableInterface)
     */
    private Closure $closure;

    public function __construct(private string $name, Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();
        $returnType = $reflection->getReturnType();
        if (count($parameters) !== 1) {
            throw new InvalidArgumentException('Callback should have exactly one parameter');
        }
        if (!($parameters[0]->getType() instanceof ReflectionNamedType) || $parameters[0]->getType()->getName() !== ResolvableVariableSet::class) {
            throw new InvalidArgumentException('First callback parameter should be a ResolvableVariableSet');
        }

        if (!($returnType instanceof ReflectionNamedType) || $returnType->getName() !== VariableInterface::class) {
            throw new InvalidArgumentException('Callback return type should be a VariableInterface.');
        }

        $this->closure = $closure;
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
