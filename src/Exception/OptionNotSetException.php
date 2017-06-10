<?php

declare(strict_types=1);

namespace Cascader\Exception;

use RuntimeException;

class OptionNotSetException extends RuntimeException implements ExceptionInterface
{
    public static function forKey(string $key)
    {
        return new self(sprintf("Option '%s' is not set", $key));
    }
}
