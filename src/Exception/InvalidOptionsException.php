<?php

declare(strict_types=1);

namespace Cascader\Exception;

use BetterReflection\Reflection\ReflectionParameter;
use InvalidArgumentException;

class InvalidOptionsException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forInvalidKeys()
    {
        return new static('Options should be in form of an associate array (string keys)');
    }

    public static function forMissingMandatoryParameter(ReflectionParameter $parameter)
    {
        $className = $parameter->getDeclaringClass()->getName();
        $parameterName = $parameter->getName();

        return new static(sprintf('Mandatory parameter: \'%2$s\' of class: %1$s is missing from options', $className, $parameterName));
    }
}
