<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class Bar
{
    /**
     * @var Baz
     */
    public $baz;

    /**
     * @var array
     */
    public $config;

    public function __construct(Baz $baz, array $config)
    {
        $this->baz = $baz;
        $this->config = $config;
    }
}
