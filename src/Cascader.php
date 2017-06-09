<?php

declare(strict_types=1);

namespace Cascader;

use BetterReflection\Reflection\ReflectionClass;
use BetterReflection\Reflection\ReflectionParameter;
use BetterReflection\Reflector\Exception\IdentifierNotFound;
use Cascader\Exception\InvalidClassException;
use Cascader\Exception\InvalidOptionsException;
use Cascader\Exception\OptionNotSetException;
use phpDocumentor\Reflection\Types\Object_;

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

    protected function getReflectionClass(string $className)
    {
        try {
            $reflectionClass = ReflectionClass::createFromName($className);
        } catch (IdentifierNotFound $ex) {
            throw InvalidClassException::forNonExistingClass($className);
        }

        if (! $reflectionClass->isInstantiable()) {
            throw InvalidClassException::forNonInstantiableClass($className);
        }

        return $reflectionClass;
    }

    protected function marshalArguments(ReflectionClass $reflectionClass, Options $options) : array
    {
        if (! $reflectionClass->hasMethod('__construct')) {
            return [];
        }

        $arguments = [];

        $constructorParameters = $reflectionClass->getConstructor()->getParameters();

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
                $parameterTypeObject = $parameterType->getTypeObject();

                if (is_array($argument) && $parameterTypeObject instanceof Object_) {
                    $argument = $this->create((string) $parameterTypeObject->getFqsen(), $argument);
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
