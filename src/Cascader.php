<?php

declare(strict_types=1);

namespace Cascader;

use Cascader\Exception\InvalidClassException;
use Cascader\Exception\InvalidOptionsException;
use Cascader\Exception\OptionNotSetException;
use ReflectionClass;
use ReflectionParameter;
use ReflectionType;

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
            $argument = $options->get($parameter->name);

            if (null !== ($parameterType = $parameter->getType()) && $this->shouldResolveObjectArgument($parameterType, $argument)) {
                $argument = $this->resolveObjectArgument((string) $parameterType, $argument);
            }

            return $argument;
        } catch (OptionNotSetException $ex) {
            if (! $parameter->isOptional()) {
                throw InvalidOptionsException::forMissingMandatoryParameter($parameter);
            }

            return $parameter->getDefaultValue();
        }
    }

    protected function shouldResolveObjectArgument(ReflectionType $parameterType, $argument) : bool
    {
        return !$parameterType->isBuiltin() && \is_array($argument);
    }

    protected function resolveObjectArgument(string $className, array $arguments)
    {
        if (isset($arguments['__class__'])) {
            $className = $arguments['__class__'];
            unset($arguments['__class__']);
        }

        return $this->create($className, $arguments);
    }
}
