<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Cascader\Cascader;

class Foo
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

$obj1 = $cascader->create(Foo::class, [
    'name' => 'foo1',
    'count' => 10,
    'isActive' => true,
]);
var_dump($obj1);

//option keys automatically normalized to camel-case
$obj2 = $cascader->create(Foo::class, [
    'name' => 'foo2',
    'count' => 10,
    'is_active' => true,
]);
var_dump($obj2);

//random order of parameters
$obj3 = $cascader->create(Foo::class, [
    'count' => 1,
    'is_active' => true,
    'name' => 'foo3',
]);
var_dump($obj3);

//optional parameters can be omitted
$obj4 = $cascader->create(Foo::class, [
    'name' => 'foo4',
    'is_active' => true,
]);
var_dump($obj4);
