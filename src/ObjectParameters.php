<?php

declare(strict_types=1);

namespace Cascader;

use BetterReflection\Reflection\ReflectionParameter;
use BetterReflection\Reflection\ReflectionClass;

class ObjectParameters
{
    /**
     * @var ReflectionParameter[]
     */
    protected $parameters;

    protected function __construct(ReflectionParameter ...$parameters)
    {
        $this->parameters = $parameters;
    }

    public static function fromReflectionClass(ReflectionClass $reflectionClass)
    {
        if (!$reflectionClass->hasMethod('__construct')) {
            return new static();
        }

        $parameters = $reflectionClass->getConstructor()->getParameters();

        return new static(...$parameters);
    }

    public function getAll() : array
    {
        return $this->parameters;
    }
}
