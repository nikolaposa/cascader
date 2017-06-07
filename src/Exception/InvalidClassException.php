<?php

declare(strict_types=1);

namespace Cascader\Exception;

use RuntimeException;

class InvalidClassException extends RuntimeException implements ExceptionInterface
{
    public static function forNonExistingClass(string $className)
    {
        return new static(sprintf('%s class does not exist', $className));
    }

    public static function forNonInstantiableClass(string $className)
    {
        return new static(sprintf('%s class cannot be instantiated', $className));
    }
}
