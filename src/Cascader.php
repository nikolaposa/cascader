<?php

declare(strict_types=1);

namespace Cascader;

use Cascader\Exception\InvalidClassException;
use Cascader\Exception\InvalidOptionsException;
use ReflectionClass;
use ReflectionParameter;

class Cascader
{
    const ARGUMENT_CLASS = '__class__';

    /**
     * @param string $className
     * @param array $options
     *
     * @throws InvalidClassException
     * @throws InvalidOptionsException
     *
     * @return object
     */
    public function create(string $className, array $options)
    {
        $reflectionClass = $this->getReflectionClass($className);
        $options = Options::fromArray($options);

        $arguments = $this->resolveArguments($reflectionClass, $options);

        return new $className(...$arguments);
    }

    protected function getReflectionClass(string $className) : ReflectionClass
    {
        try {
            $reflectionClass = new ReflectionClass($className);
        } catch (\ReflectionException $ex) {
            throw InvalidClassException::nonExistingClass($className);
        }

        if (! $reflectionClass->isInstantiable()) {
            throw InvalidClassException::nonInstantiableClass($className);
        }

        return $reflectionClass;
    }

    protected function resolveArguments(ReflectionClass $reflectionClass, Options $options) : array
    {
        $constructor = $reflectionClass->getConstructor();

        if (null === $constructor) {
            return [];
        }

        $arguments = [];

        $constructorParameters = $constructor->getParameters();

        foreach ($constructorParameters as $parameter) {
            $arguments[] = $this->resolveArgument($parameter, $options);
        }

        return $arguments;
    }

    protected function resolveArgument(ReflectionParameter $reflectionParameter, Options $options)
    {
        $parameterAnalysis = new ParameterAnalysis($reflectionParameter, $options);

        if (! $parameterAnalysis->hasArgument()) {
            if (! $parameterAnalysis->isOptional()) {
                throw InvalidOptionsException::missingMandatoryParameter($parameterAnalysis);
            }

            return $parameterAnalysis->getDefaultValue();
        }

        $argument = $parameterAnalysis->getArgument();

        if ($parameterAnalysis->isArray()) {
            return $this->resolveArrayArgument($argument);
        }

        if ($parameterAnalysis->isNestedObject()) {
            return $this->createNestedObject($parameterAnalysis->getType(), $argument);
        }

        return $argument;
    }

    protected function resolveArrayArgument(array $argument) : array
    {
        foreach ($argument as $k => $value) {
            if (\is_array($value) && isset($value[self::ARGUMENT_CLASS])) {
                $argument[$k] = $this->createNestedObject('', $value);
            }
        }

        return $argument;
    }

    protected function createNestedObject(string $className, array $arguments)
    {
        if (isset($arguments[self::ARGUMENT_CLASS])) {
            $className = $arguments[self::ARGUMENT_CLASS];
            unset($arguments[self::ARGUMENT_CLASS]);
        }

        return $this->create($className, $arguments);
    }
}
