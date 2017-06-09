<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Cascader\Cascader;

class RootObject
{
    public $subObject;

    public $data;

    public function __construct(SubObject $subObject, array $data)
    {
        $this->subObject = $subObject;
        $this->data = $data;
    }
}

class SubObject
{
    public $name;

    public $count;

    public $isActive;

    public function __construct(string $name, int $count = 3, $isActive = false)
    {
        $this->name = $name;
        $this->count = $count;
        $this->isActive = $isActive;
    }
}

$cascader = new Cascader();

$object = $cascader->create(RootObject::class, [
    'sub_object' => [
        'name' => 'test',
        'count' => 10,
        'is_active' => true,
    ],
    'data' => [
        'foo' => 'bar',
    ],
]);
var_dump($object);
