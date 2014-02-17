<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace ClassResolverTest;

use ClassResolver\ClassResolver;
use ClassResolverTest\Fake\Foo;
use Zend\ServiceManager\ServiceManager;

class ClassResolverTest extends \PHPUnit_Framework_TestCase
{

    protected $classResolver, $serviceLocatorMock;

    public function setUp()
    {
        parent::setUp();

        $this->classResolver = new ClassResolver([
            'ClassResolverTest\Fake\FailInterface'      => 'ClassResolverTest\Fake\Foo',
            'ClassResolverTest\Fake\FooInterface'       => 'ClassResolverTest\Fake\Foo',
            'ClassResolverTest\Fake\BarInterface'       => 'ClassResolverTest\Fake\Bar',
            'ClassResolverTest\Fake\SuperFailInterface' => 'ClassResolverTest\Fake\SuperFail',
        ]);
    }

    public function testResolveClassName()
    {
        $this->assertEquals(
            'ClassResolverTest\Fake\Foo',
            $this->classResolver->resolveClassName('ClassResolverTest\Fake\FooInterface')
        );
        $this->assertEquals(
            'ClassResolverTest\Fake\Bar',
            $this->classResolver->resolveClassName('ClassResolverTest\Fake\BarInterface')
        );
    }

    public function testResolve()
    {
        $this->assertInstanceOf(
            'ClassResolverTest\Fake\FooInterface',
            $this->classResolver->resolve('ClassResolverTest\Fake\FooInterface')
        );
        $this->assertNotSame(
            $this->classResolver->resolve('ClassResolverTest\Fake\FooInterface'),
            $this->classResolver->resolve('ClassResolverTest\Fake\FooInterface')
        );
    }

    public function testResolveFromServiceManager()
    {
        $serviceManager = new ServiceManager();
        $service        = new Foo();

        $serviceManager->setService('ClassResolverTest\Fake\Foo', $service);
        $this->classResolver->setServiceLocator($serviceManager);

        $this->assertSame($service, $this->classResolver->resolve('ClassResolverTest\Fake\FooInterface', true));
    }

    /**
     * @expectedException \ClassResolver\Exception\RuntimeException
     */
    public function testResolveException()
    {
        $this->classResolver->resolve('ClassResolverTest\Fake\FailInterface');
    }

    /**
     * @expectedException \ClassResolver\Exception\InvalidArgumentException
     */
    public function testInvalidArgumentException()
    {
        $this->classResolver->resolveClassName([]);
    }

    /**
     * @expectedException \ClassResolver\Exception\RuntimeException
     */
    public function testClassNotFound()
    {
        $this->classResolver->resolveClassName('ClassResolverTest\Fake\SuperFailInterface');
    }

    /**
     * @expectedException \ClassResolver\Exception\RuntimeException
     */
    public function testNotResolvable()
    {
        $this->classResolver->resolveClassName('NotResolvable');
    }
}