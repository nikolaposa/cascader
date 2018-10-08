<?php

declare(strict_types=1);

namespace Cascader\Exception;

use Cascader\ParameterAnalysis;
use InvalidArgumentException;

final class InvalidOptionsException extends InvalidArgumentException implements ExceptionInterface
{
    public static function invalidKeys()
    {
        return new self('Options should be in form of an associate array (string keys)');
    }

    public static function missingMandatoryParameter(ParameterAnalysis $parameterAnalysis)
    {
        return new self(sprintf(
            'Mandatory parameter: \'%2$s\' of class: %1$s is missing from options',
            $parameterAnalysis->getDeclaringClass(),
            $parameterAnalysis->getName()
        ));
    }
}
