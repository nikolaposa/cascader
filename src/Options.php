<?php

declare(strict_types=1);

namespace Cascader;

use Cascader\Exception\InvalidOptionsException;
use Cascader\Exception\OptionNotSetException;

class Options
{
    /**
     * @var array
     */
    protected $options;

    protected function __construct(array $options)
    {
        $this->options = $options;
    }

    public static function fromArray(array $options)
    {
        self::validate($options);
        $options = self::normalize($options);

        return new static($options);
    }

    final protected static function validate(array $options)
    {
        foreach ($options as $key => $value) {
            if (! \is_string($key)) {
                throw InvalidOptionsException::invalidKeys();
            }
        }
    }

    final protected static function normalize(array $options) : array
    {
        $normalizedOptions = [];

        foreach ($options as $key => $value) {
            $normalizedOptions[$key] = $value;

            $normalizedKey = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $key)));
            $normalizedKey[0] = strtolower($normalizedKey[0]);
            $normalizedOptions[$normalizedKey] = $value;
        }

        return $normalizedOptions;
    }

    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->options);
    }

    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw OptionNotSetException::forKey($key);
        }

        return $this->options[$key];
    }
}
