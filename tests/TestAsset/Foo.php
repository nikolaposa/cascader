<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class Foo
{
    /**
     * @var Bar
     */
    public $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
