<?php

declare(strict_types=1);

namespace Tests;

use DI\Container as DIContainer;
use Lion\DependencyInjection\Container;
use Lion\Helpers\Str;
use Lion\Test\Test;
use ReflectionMethod;
use ReflectionParameter;
use Tests\Provider\CustomClass;
use Tests\Provider\FactoryProvider;

class ContainerTest extends Test
{
    const FOLDER = './tests/';
    const PATH_FILE = './Provider/CustomClass.php';
    const FILES = [
        '/var/www/html/tests/ContainerTest.php',
        '/var/www/html/tests/Provider/CustomClass.php',
        '/var/www/html/tests/Provider/FactoryProvider.php'
    ];
    const REFLECTION_PARAMETERS = [CustomClass::class, 'setFactoryProvider'];

    private Container $container;
    private CustomClass $customClass;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->customClass = new CustomClass();

        $this->initReflection($this->container);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(DIContainer::class, $this->getPrivateProperty('container'));
        $this->assertInstanceOf(Str::class, $this->getPrivateProperty('str'));
    }

    public function testGetFiles(): void
    {
        $files = $this->container->getFiles(self::FOLDER);

        $this->assertIsArray($files);
        $this->assertSame(self::FILES, $files);
    }

    public function testGetNamespace(): void
    {
        $namespace = $this->container->getNamespace(self::PATH_FILE, 'Tests\\Provider\\', 'Provider/');

        $this->assertIsString($namespace);
        $this->assertSame(CustomClass::class, $namespace);
    }

    public function testGetParameters(): void
    {
        $class = new class {
            public function exampleMethod(FactoryProvider $factoryProvider): FactoryProvider
            {
                return $factoryProvider;
            }
        };

        $parameters = $this->getPrivateMethod('getParameters', [new ReflectionMethod($class, 'exampleMethod')]);

        $this->assertIsArray($parameters);
        $this->assertInstanceOf(FactoryProvider::class, reset($parameters));
    }

    public function testInjectDependencies(): void
    {
        /** @var CustomClass $customClass */
        $customClass = $this->container->injectDependencies($this->customClass);

        $this->initReflection($customClass);

        $this->assertInstanceOf(CustomClass::class, $customClass);
        $this->assertInstanceOf(FactoryProvider::class, $customClass->getFactoryProvider());
    }

    public function testGetParameterClassName()
    {
        $reflectionParameter = new ReflectionParameter(self::REFLECTION_PARAMETERS, 'factoryProvider');
        $result = $this->getPrivateMethod('getParameterClassName', [$reflectionParameter]);

        $this->assertSame(FactoryProvider::class, $result);
    }
}
