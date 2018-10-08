<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class SubObjectsCollectionAsset
{
    /**
     * @var SubObjectAsset[]
     */
    public $collection = [];

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }
}
