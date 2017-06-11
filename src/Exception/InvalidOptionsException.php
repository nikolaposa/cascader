<?php

declare(strict_types=1);

namespace Cascader\Exception;

use InvalidArgumentException;
use ReflectionParameter;

class InvalidOptionsException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forInvalidKeys()
    {
        return new self('Options should be in form of an associate array (string keys)');
    }

    public static function forMissingMandatoryParameter(ReflectionParameter $parameter)
    {
        $className = $parameter->getDeclaringClass()->name;
        $parameterName = $parameter->getName();

        return new self(sprintf('Mandatory parameter: \'%2$s\' of class: %1$s is missing from options', $className, $parameterName));
    }
}
