<?php

declare(strict_types=1);

namespace Cascader\Tests;

use PHPUnit\Framework\TestCase;
use Cascader\Cascader;
use Cascader\Tests\TestAsset\Foo;
use Cascader\Tests\TestAsset\Bar;
use Cascader\Tests\TestAsset\Baz;
use Cascader\Tests\TestAsset\AbstractClassAsset;
use Cascader\Tests\TestAsset\InvokableAsset;
use Cascader\Exception\InvalidClassException;
use Cascader\Exception\InvalidOptionsException;

class CascaderTest extends TestCase
{
    /**
     * @var Cascader
     */
    protected $cascader;

    protected function setUp()
    {
        $this->cascader = new Cascader();
    }

    /**
     * @test
     */
    public function it_creates_object_with_simple_creation_options()
    {
        $object = $this->cascader->create(Baz::class, [
            'name' => 'test',
            'count' => 10,
        ]);

        $this->assertInstanceOf(Baz::class, $object);
        $this->assertEquals('test', $object->name);
        $this->assertEquals(10, $object->count);
    }

    /**
     * @test
     */
    public function it_raises_exception_if_class_does_not_exist()
    {
        try {
            $this->cascader->create('NonExisting', [
                'name' => 'test',
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidClassException $ex) {
            $this->assertEquals('NonExisting class does not exist', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_class_cannot_be_instantiated()
    {
        try {
            $this->cascader->create(AbstractClassAsset::class, [
                'name' => 'test',
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidClassException $ex) {
            $this->assertEquals('Cascader\Tests\TestAsset\AbstractClassAsset class cannot be instantiated', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_creation_options_is_not_associative_array()
    {
        try {
            $this->cascader->create(Baz::class, ['invalid']);

            $this->fail('Exception should have been raised');
        } catch (InvalidOptionsException $ex) {
            $this->assertEquals('Options should be in form of an associate array (string keys)', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_creates_object_that_has_an_empty_constructor()
    {
        $object = $this->cascader->create(InvokableAsset::class, []);

        $this->assertInstanceOf(InvokableAsset::class, $object);
    }

    /**
     * @test
     */
    public function it_normalizes_creation_option_keys_to_match_constructor_parameter_names()
    {
        $object = $this->cascader->create(Baz::class, [
            'name' => 'test',
            'count' => 10,
            'is_active' => false,
        ]);

        $this->assertInstanceOf(Baz::class, $object);
        $this->assertEquals('test', $object->name);
        $this->assertEquals(10, $object->count);
        $this->assertFalse($object->isActive);
    }

    /**
     * @test
     */
    public function it_handles_optional_parameters_regardless_of_their_order()
    {
        $object = $this->cascader->create(Baz::class, [
            'name' => 'test',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Baz::class, $object);
        $this->assertEquals('test', $object->name);
        $this->assertEquals(3, $object->count);
        $this->assertTrue($object->isActive);
    }

    /**
     * @test
     */
    public function it_raises_exception_if_mandatory_parameter_is_not_provided_in_options()
    {
        try {
            $this->cascader->create(Baz::class, [
                'count' => 10,
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidOptionsException $ex) {
            $this->assertEquals('Mandatory parameter: \'name\' of class: ' . Baz::class . ' is missing from options', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_creates_cascade_of_objects()
    {
        $object = $this->cascader->create(Foo::class, [
            'bar' => [
                'baz' => [
                    'name' => 'test',
                    'count' => 10,
                ],
                'config' => [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
            ],
        ]);

        $this->assertInstanceOf(Foo::class, $object);
        $this->assertInstanceOf(Bar::class, $object->bar);
        $this->assertEquals([
            'key1' => 'val1',
            'key2' => 'val2',
        ], $object->bar->config);
        $this->assertInstanceOf(Baz::class, $object->bar->baz);
        $this->assertEquals('test', $object->bar->baz->name);
        $this->assertEquals(10, $object->bar->baz->count);
        $this->assertTrue($object->bar->baz->isActive);
    }
}
