<?php

declare(strict_types=1);

namespace Cascader\Exception;

use RuntimeException;

final class InvalidClassException extends RuntimeException implements ExceptionInterface
{
    public static function nonExistingClass(string $className)
    {
        return new self(sprintf('%s class does not exist', $className));
    }

    public static function nonInstantiableClass(string $className)
    {
        return new self(sprintf('%s class cannot be instantiated', $className));
    }
}
