<?php

declare(strict_types=1);

namespace Cascader\Tests;

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
            $this->cascader->create(SubObjectAsset::class, ['invalid']);

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
    public function it_creates_object_regardless_of_options_casing()
    {
        $object = $this->cascader->create(SubObjectAsset::class, [
            'name' => 'test',
            'count' => 10,
            'is_active' => false,
        ]);

        $this->assertInstanceOf(SubObjectAsset::class, $object);
        $this->assertEquals('test', $object->name);
        $this->assertEquals(10, $object->count);
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
        $this->assertEquals('test', $object->name);
        $this->assertEquals(1, $object->count);
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
            $this->cascader->create(SubObjectAsset::class, [
                'count' => 10,
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidOptionsException $ex) {
            $this->assertEquals('Mandatory parameter: \'name\' of class: ' . SubObjectAsset::class . ' is missing from options', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_creates_cascade_of_objects()
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
        $this->assertEquals('test', $object->subObject->name);
        $this->assertEquals(10, $object->subObject->count);
        $this->assertTrue($object->subObject->isActive);
        $this->assertEquals([
            'key1' => 'val1',
            'key2' => 'val2',
        ], $object->data);
    }

    /**
     * @test
     * @throws \ReflectionException
     *
     * @dataProvider dataProviderForResolveClass
     */
    public function it_resolve_subject_class(array $expected, string $class, array $arguments = [])
    {
        $method = new \ReflectionMethod(Cascader::class, 'resolveClass');
        $method->setAccessible(true);
        $actual = $method->invoke($this->cascader, $class, $arguments);

        $this->assertSame($expected, $actual);
    }

    public function dataProviderForResolveClass(): array
    {
        return [
            'general' => [
                ['Some\ClassName', ['arg1' => 1, 'arg2' => true]],
                'Some\ClassName',
                ['arg1' => 1, 'arg2' => true]
            ],
            'instead interface' => [
                ['Some\ConcreteClass', ['arg' => 'value']],
                'Some\SomeInterface',
                ['__class__' => 'Some\ConcreteClass', 'options' => ['arg' => 'value']]
            ],
            'replace class from declarate type' => [
                ['Some\ReplaceClass', ['arg' => 'value']],
                'Some\DeclarateArgumentClass',
                ['__class__' => 'Some\ReplaceClass', 'options' => ['arg' => 'value']]
            ],
        ];
    }
}
