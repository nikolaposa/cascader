<?php

declare(strict_types=1);

namespace Cascader\Tests\TestAsset;

class RootObjectAsset
{
    /**
     * @var SubObjectAsset
     */
    public $subObject;

    /**
     * @var array
     */
    public $data;

    public function __construct(SubObjectAsset $subObject, array $data)
    {
        $this->subObject = $subObject;
        $this->data = $data;
    }
}
