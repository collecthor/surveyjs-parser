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
 * @package Collecthor\SurveyjsParser
 */
final class DeferredVariable
{
    private Closure $closure;
    
    public function __construct(private string $name, Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();
        $returnType = $reflection->getReturnType();
        if (!($parameters[0]->getType() instanceof ReflectionNamedType) || $parameters[0]->getType()->getName() !== 'Collecthor\SurveyjsParser\ResolvableVariableSet') {
            throw new InvalidArgumentException('First callback parameter should be a ResolvableVariableSet');
        }

        if (!($returnType instanceof ReflectionNamedType) || $returnType->getName() !== "Collecthor\DataInterfaces\VariableInterface") {
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
        /** @var VariableInterface $result */
        $result = ($this->closure)($variables);
        return $result;
    }
}
