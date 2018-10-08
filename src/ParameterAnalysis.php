<?php

declare(strict_types=1);

namespace Cascader;

use Cascader\Exception\OptionNotSetException;
use ReflectionParameter;
use ReflectionType;

final class ParameterAnalysis
{
    /**
     * @var ReflectionParameter
     */
    private $parameter;

    /**
     * @var ReflectionType|null
     */
    private $parameterType;

    /**
     * @var string
     */
    private $parameterTypeName;

    /**
     * @var mixed
     */
    private $argument;

    /**
     * @var bool
     */
    private $hasArgument;

    public function __construct(ReflectionParameter $parameter, Options $options)
    {
        $this->parameter = $parameter;
        $this->setParameterType();
        $this->setArgument($options);
    }

    public function getName() : string
    {
        return $this->parameter->getName();
    }

    public function getType() : string
    {
        return $this->parameterTypeName;
    }

    public function hasArgument() : bool
    {
        return $this->hasArgument;
    }

    public function getArgument()
    {
        return $this->argument;
    }

    public function isOptional() : bool
    {
        return $this->parameter->isOptional();
    }

    public function getDefaultValue()
    {
        return $this->parameter->getDefaultValue();
    }

    public function isNestedObject() : bool
    {
        return null !== $this->parameterType && !$this->parameterType->isBuiltin() && \is_array($this->argument);
    }

    public function isArray() : bool
    {
        return 'array' === $this->parameterTypeName && \is_array($this->argument);
    }

    public function getDeclaringClass() : string
    {
        return $this->parameter->getDeclaringClass()->name;
    }

    private function setParameterType()
    {
        if (null !== ($parameterType = $this->parameter->getType())) {
            $this->parameterType = $parameterType;
            $this->parameterTypeName = method_exists($parameterType, 'getName')
                ? $parameterType->getName() //PHP 7.1
                : (string) $parameterType;
        }
    }

    private function setArgument(Options $options)
    {
        try {
            $this->argument = $options->get($this->parameter->name);
            $this->hasArgument = true;
        } catch (OptionNotSetException $ex) {
            $this->hasArgument = false;
        }
    }
}
