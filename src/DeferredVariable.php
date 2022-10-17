<?php
declare(strict_types=1);

namespace Collecthor\SurveyjsParser;

use Closure;
use Collecthor\DataInterfaces\VariableInterface;
use InvalidArgumentException;
use ReflectionFunction;

/**
 * @internal
 * @package Collecthor\SurveyjsParser
 */
final class DeferredVariable {

    private Closure $closure;
    
    public function __construct(private string $name, Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();

        if((string)$parameters[0]->getType() !== 'SearchableVariableSet') {
            throw new InvalidArgumentException('First callback parameter should be a SearchableVariableSet');
        }

        if ((string)$reflection->getReturnType() !== "VariableInterface") {
            throw new InvalidArgumentException('Callback return type should be a VariableInterface.');
        }

        $this->closure = $closure;
    }

    public function getName(): string{
        return $this->name;
    }

    public function resolve(SearchableVariableSet $variables): VariableInterface {
        /** @var VariableInterface $result */
        $result = call_user_func($this->closure, $variables);
        return $result;
    }
}