<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Cascader\Cascader;

class RootObject
{
    public $test;

    public function __construct(TestInterface $test)
    {
        $this->test = $test;
    }
}

interface TestInterface
{
    public function foo();
}

class Test implements TestInterface
{
    public $bar;

    public $baz;

    public function __construct(string $bar, int $baz = 3)
    {
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function foo()
    {
    }
}

$cascader = new Cascader();

$object = $cascader->create(RootObject::class, [
    'test' => [
        '__class__' => Test::class,
        'bar' => 'test',
        'baz' => 10,
    ],
]);
var_dump($object);
