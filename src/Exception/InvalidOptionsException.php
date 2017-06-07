<?php

declare(strict_types=1);

namespace Cascader\Exception;

use InvalidArgumentException;

class InvalidOptionsException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forInvalidKeys()
    {
        return new static('Options should be in form of an associate array (string keys)');
    }
}
