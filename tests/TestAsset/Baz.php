<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class Baz
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $count;

    /**
     * @var bool
     */
    public $isActive;
    
    public function __construct(string $name, int $count = 3, $isActive = true)
    {
        $this->name = $name;
        $this->count = $count;
        $this->isActive = $isActive;
    }
}
