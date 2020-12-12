<?php

declare(strict_types=1);

namespace Cascader\Tests;

use Cascader\Tests\TestAsset\SubObjectsCollectionAsset;
use Cascader\Tests\TestAsset\CustomSubObjectAsset;
use PHPUnit\Framework\TestCase;
use Cascader\Cascader;
use Cascader\Tests\TestAsset\RootObjectAsset;
use Cascader\Tests\TestAsset\SubObjectAsset;
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
        $object = $this->cascader->create(SubObjectAsset::class, [
            'name' => 'test',
            'count' => 10,
        ]);

        $this->assertInstanceOf(SubObjectAsset::class, $object);
        $this->assertSame('test', $object->name);
        $this->assertSame(10, $object->count);
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
            $this->assertSame('NonExisting class does not exist', $ex->getMessage());
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
            $this->assertSame('Cascader\Tests\TestAsset\AbstractClassAsset class cannot be instantiated', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_creation_options_is_not_associative_array()
    {
        try {
            $this->cascader->create(SubObjectAsset::class, ['invalid']);

            $this->fail('Exception should have been raised');
        } catch (InvalidOptionsException $ex) {
            $this->assertSame('Options should be in form of an associate array (string keys)', $ex->getMessage());
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
    public function it_creates_object_regardless_of_options_casing()
    {
        $object = $this->cascader->create(SubObjectAsset::class, [
            'name' => 'test',
            'count' => 10,
            'is_active' => false,
        ]);

        $this->assertInstanceOf(SubObjectAsset::class, $object);
        $this->assertSame('test', $object->name);
        $this->assertSame(10, $object->count);
        $this->assertFalse($object->isActive);
    }

    /**
     * @test
     */
    public function it_allows_option_to_be_set_in_any_order()
    {
        $object = $this->cascader->create(SubObjectAsset::class, [
            'count' => 1,
            'is_active' => false,
            'name' => 'test',
        ]);

        $this->assertInstanceOf(SubObjectAsset::class, $object);
        $this->assertSame('test', $object->name);
        $this->assertSame(1, $object->count);
        $this->assertFalse($object->isActive);
    }

    /**
     * @test
     */
    public function it_handles_optional_parameters_regardless_of_their_order()
    {
        $object = $this->cascader->create(SubObjectAsset::class, [
            'name' => 'test',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(SubObjectAsset::class, $object);
        $this->assertSame('test', $object->name);
        $this->assertSame(3, $object->count);
        $this->assertTrue($object->isActive);
    }

    /**
     * @test
     */
    public function it_raises_exception_if_mandatory_parameter_is_not_provided_in_options()
    {
        try {
            $this->cascader->create(SubObjectAsset::class, [
                'count' => 10,
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidOptionsException $ex) {
            $this->assertSame('Mandatory parameter: \'name\' of class: ' . SubObjectAsset::class . ' is missing from options', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_handles_creation_of_object_arguments()
    {
        $object = $this->cascader->create(RootObjectAsset::class, [
            'sub_object' => [
                'name' => 'test',
                'count' => 10,
            ],
            'data' => [
                'key1' => 'val1',
                'key2' => 'val2',
            ],
        ]);

        $this->assertInstanceOf(RootObjectAsset::class, $object);
        $this->assertInstanceOf(SubObjectAsset::class, $object->subObject);
        $this->assertSame('test', $object->subObject->name);
        $this->assertSame(10, $object->subObject->count);
        $this->assertTrue($object->subObject->isActive);
        $this->assertSame([
            'key1' => 'val1',
            'key2' => 'val2',
        ], $object->data);
    }

    /**
     * @test
     */
    public function it_allows_passing_concrete_class_name_for_objects_via_arguments()
    {
        $object = $this->cascader->create(RootObjectAsset::class, [
            'sub_object' => [
                Cascader::ARGUMENT_CLASS => CustomSubObjectAsset::class,
                'name' => 'test',
                'count' => 10,
            ],
            'data' => [
                'key1' => 'val1',
                'key2' => 'val2',
            ],
        ]);

        $this->assertInstanceOf(RootObjectAsset::class, $object);
        $this->assertInstanceOf(CustomSubObjectAsset::class, $object->subObject);
        $this->assertSame('test', $object->subObject->name);
        $this->assertSame(10, $object->subObject->count);
    }

    /**
     * @test
     */
    public function it_allows_passing_concrete_class_name_for_sub_object_collections()
    {
        $object = $this->cascader->create(SubObjectsCollectionAsset::class, [
            'collection' => [
                [
                    Cascader::ARGUMENT_CLASS => CustomSubObjectAsset::class,
                    'name' => 'test',
                    'count' => 10,
                ],
                [
                    Cascader::ARGUMENT_CLASS => CustomSubObjectAsset::class,
                    'name' => 'test2',
                    'count' => 20,
                ]
            ]
        ]);

        $this->assertInstanceOf(SubObjectsCollectionAsset::class, $object);
        $this->assertCount(2, $object->collection);
        $this->assertInstanceOf(SubObjectAsset::class, $object->collection[0]);
        $this->assertSame('test', $object->collection[0]->name);
        $this->assertSame(10, $object->collection[0]->count);
        $this->assertInstanceOf(SubObjectAsset::class, $object->collection[1]);
        $this->assertSame('test2', $object->collection[1]->name);
        $this->assertSame(20, $object->collection[1]->count);
    }
}
