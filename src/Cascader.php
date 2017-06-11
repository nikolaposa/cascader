<?php

declare(strict_types=1);

namespace Cascader;

use Cascader\Exception\InvalidClassException;
use Cascader\Exception\InvalidOptionsException;
use Cascader\Exception\OptionNotSetException;
use ReflectionClass;
use ReflectionParameter;

class Cascader
{
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

        $arguments = $this->marshalArguments($reflectionClass, $options);

        return new $className(...$arguments);
    }

    protected function getReflectionClass(string $className) : ReflectionClass
    {
        try {
            $reflectionClass = new ReflectionClass($className);
        } catch (\ReflectionException $ex) {
            throw InvalidClassException::forNonExistingClass($className);
        }

        if (! $reflectionClass->isInstantiable()) {
            throw InvalidClassException::forNonInstantiableClass($className);
        }

        return $reflectionClass;
    }

    protected function marshalArguments(ReflectionClass $reflectionClass, Options $options) : array
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

    protected function resolveArgument(ReflectionParameter $parameter, Options $options)
    {
        try {
            $argument = $options->get($parameter->getName());

            if (null !== ($parameterType = $parameter->getType())) {
                if (is_array($argument) && ! $parameterType->isBuiltin()) {
                    $argument = $this->create((string) $parameterType, $argument);
                }
            }

            return $argument;
        } catch (OptionNotSetException $ex) {
            if (! $parameter->isOptional()) {
                throw InvalidOptionsException::forMissingMandatoryParameter($parameter);
            }

            return $parameter->getDefaultValue();
        }
    }
}
