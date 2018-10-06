<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class ArraySubObjectsAsset
{
    /** @var array|SubObjectAsset[] */
    public $child = [];

    /**
     * @param SubObjectAsset[] $child
     */
    public function __construct(array $child)
    {
        $this->child = $child;
    }
}
