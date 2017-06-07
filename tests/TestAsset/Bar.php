<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class Bar
{
    /**
     * @var Bar
     */
    public $baz;

    /**
     * @var array
     */
    public $config;

    public function __construct(Bar $baz, array $config)
    {
        $this->baz = $baz;
        $this->config = $config;
    }
}
