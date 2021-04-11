<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;

use PHPUnit\Framework\TestCase;

function test1(Foo $foo) {}

class TestClass
{
    public function aMethod(Foo $foo) {}

    public static function aStaticMethod(Foo $foo) {}
}

class ParameterDeriverTest extends TestCase
{

    protected $deriver;

    public function setUp(): void
    {
        parent::setUp();

        $this->deriver = new class {
            use ParameterDeriverTrait {
                getParameterType as public;
            }
        };
    }

    public function test_derive_function() : void
    {
        $type = $this->deriver->getParameterType(__NAMESPACE__ . '\\test1');

        $this->assertEquals(Foo::class, $type);
    }

    public function test_derive_closure() : void
    {
        $type = $this->deriver->getParameterType(function(Foo $foo) {});

        $this->assertEquals(Foo::class, $type);
    }

    public function test_derive_method() : void
    {
        $test = new TestClass();
        $type = $this->deriver->getParameterType([$test, 'aMethod']);

        $this->assertEquals(Foo::class, $type);
    }

    public function test_derive_static_method() : void
    {
        $type = $this->deriver->getParameterType([TestClass::class, 'aStaticMethod']);

        $this->assertEquals(Foo::class, $type);
    }

    public function test_derive_method_without_instantiating() : void
    {
        $type = $this->deriver->getParameterType([TestClass::class, 'aMethod']);

        $this->assertEquals(Foo::class, $type);
    }

    public function test_derive_invokable() : void
    {
        $type = $this->deriver->getParameterType(new Invokable());

        $this->assertEquals(Foo::class, $type);
    }
}
