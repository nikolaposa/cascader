<?php

declare(strict_types=1);

namespace Cascader\Tests;

use PHPUnit\Framework\TestCase;
use Cascader\Cascader;
use Cascader\Tests\TestAsset\Baz;
use Cascader\Tests\TestAsset\MyClass;

class CascaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_object_with_scalar_options()
    {
        $object = Cascader::create(Baz::class, [
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
    public function it_normalizes_option_keys_to_match_parameter_names_for_creation()
    {
        $object = Cascader::create(Baz::class, [
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
    public function it_creates_object_that_has_an_empty_constructor()
    {
        $object = Cascader::create(MyClass::class, []);

        $this->assertInstanceOf(MyClass::class, $object);
    }
}
