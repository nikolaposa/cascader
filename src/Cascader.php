<?php

declare(strict_types=1);

namespace Cascader;

use BetterReflection\Reflection\ReflectionClass;
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
    public static function create(string $className, array $options)
    {
        if (!class_exists($className)) {
            throw InvalidClassException::forNonExistingClass($className);
        }

        $reflectionClass = ReflectionClass::createFromName($className);

        if (!$reflectionClass->isInstantiable()) {
            throw InvalidClassException::forNonInstantiableClass($className);
        }

        $options = Options::fromArray($options);

        $arguments = self::marshalArguments($options, $reflectionClass);

        return new $className(...$arguments);
    }

    protected static function marshalArguments(Options $options, ReflectionClass $reflectionClass) : array
    {
        if (!$reflectionClass->hasMethod('__construct')) {
            return [];
        }

        $arguments = [];

        $className = $reflectionClass->getName();
        $constructorParameters = $reflectionClass->getConstructor()->getParameters();

        foreach ($constructorParameters as $parameter) {
            /* @var $parameter \BetterReflection\Reflection\ReflectionParameter */
            $parameterName = $parameter->getName();

            $argument = null;
            try {
                $argument = $options->get($parameterName);

                if (null !== ($parameterType = $parameter->getType())) {
                    $parameterTypeObject = $parameterType->getTypeObject();

                    if (is_array($argument) && $parameterTypeObject instanceof Object_) {
                        $argument = static::create((string) $parameterTypeObject->getFqsen(), $argument);
                    }
                }
            } catch (OptionNotSetException $ex) {
                if (!$parameter->isOptional()) {
                    throw InvalidOptionsException::forMissingMandatoryParameter($className, $parameterName);
                }

                $argument = $parameter->getDefaultValue();
            }

            $arguments[] = $argument;
        }

        return $arguments;
    }
}
