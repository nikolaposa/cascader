<?php

declare(strict_types=1);

namespace Cascader;

use BetterReflection\Reflection\ReflectionClass;
use Cascader\Exception\InvalidClassException;
use Cascader\Exception\OptionNotSetException;
use phpDocumentor\Reflection\Types\Object_;

class Cascader
{
    public static function create(string $className, array $options)
    {
        if (!class_exists($className)) {
            throw InvalidClassException::forNonExistingClass($className);
        }

        $reflectionClass = ReflectionClass::createFromName($className);

        if (!$reflectionClass->isInstantiable()) {
            throw InvalidClassException::forNonExistingClass($className);
        }

        $options = Options::fromArray($options);
        $objectParameters = ObjectParameters::fromReflectionClass($reflectionClass);

        $options = self::marshalOptions($options, $objectParameters);

        return new $className(...$options->toArgs());
    }

    protected static function marshalOptions(Options $options, ObjectParameters $objectParameters) : Options
    {
        $newOptions = [];

        foreach ($objectParameters->getAll() as $parameter) {
            /* @var $parameter \BetterReflection\Reflection\ReflectionParameter */
            $parameterName = $parameter->getName();

            $option = null;
            $hasOption = true;
            try {
                $option = $options->get($parameterName);
            } catch (OptionNotSetException $ex) {
                $hasOption = false;
            }

            if (!$hasOption) {
                if (!$parameter->isOptional()) {
                    //throw
                }

                $option = $parameter->getDefaultValue();
            } else {
                if (null !== ($parameterType = $parameter->getType())) {
                    $optionType = is_object($option) ? get_class($option) : gettype($option);

                    if ($optionType !== (string) $parameterType) {
                        if (is_array($option) && $parameterType instanceof Object_) {
                            $option = static::create($parameterType->getFqsen(), $option);
                        } else {
                            //throw
                        }
                    }
                }
            }

            $newOptions[$parameterName] = $option;
        }

        return Options::fromArray($newOptions);
    }
}
