<?php

declare(strict_types=1);

namespace Tests;

use DI\Container as DIContainer;
use Lion\DependencyInjection\Container;
use Lion\Helpers\Str;
use Lion\Test\Test;
use ReflectionMethod;
use ReflectionParameter;
use Tests\Provider\ClassProvider;
use Tests\Provider\CustomClass;
use Tests\Provider\ExtendsProvider;
use Tests\Provider\FactoryProvider;

class ContainerTest extends Test
{
    const STR = 'test';
    const FOLDER = './tests/';
    const PATH_FILE = './Provider/CustomClass.php';
    const FILES = [
        '/var/www/html/tests/ContainerTest.php',
        '/var/www/html/tests/Provider/ClassProvider.php',
        '/var/www/html/tests/Provider/CustomClass.php',
        '/var/www/html/tests/Provider/ExtendsProvider.php',
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
        $parameters = $this->getPrivateMethod(
            'getParameters',
            [new ReflectionMethod(new CustomClass(), 'setFactoryProvider')
        ]);

        $this->assertIsArray($parameters);
        $this->assertInstanceOf(FactoryProvider::class, reset($parameters));

        $parameters = $this->getPrivateMethod(
            'getParameters',
            [new ReflectionMethod(new CustomClass(), 'setMultiple')
        ]);

        $this->assertIsArray($parameters);

        $first = reset($parameters);
        $second = end($parameters);

        $this->assertInstanceOf(FactoryProvider::class, $first);
        $this->assertIsString($second);
        $this->assertSame(self::STR, $second);
    }

    public function testInjectDependenciesMethod(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesMethod($this->customClass, 'setFactoryProviderSecond');

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
    }

    public function testInjectDependenciesMethodWithMultipleArguments(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesMethod($this->customClass, 'setMultiple');

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
        $this->assertSame(self::STR, $factoryProvider->getStr());
    }

    public function testInjectDependenciesMethodWithMultipleArgumentsDefault(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesMethod(
            $this->customClass,
            'setDefaults',
            ['str' => self::STR]
        );

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
        $this->assertSame(self::STR, $factoryProvider->getStr());
    }

    public function testInjectDependenciesCallback(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesCallback(
            fn(FactoryProvider $factoryProvider, string $str): FactoryProvider => $factoryProvider->setStr($str),
            ['str' => self::STR]
        );

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
        $this->assertSame(self::STR, $factoryProvider->getStr());
    }

    public function testInjectDependencies(): void
    {
        /** @var CustomClass $customClass */
        $customClass = $this->container->injectDependencies($this->customClass);

        $this->assertInstanceOf(CustomClass::class, $customClass);
        $this->assertInstanceOf(FactoryProvider::class, $customClass->getFactoryProvider());
    }

    public function testInjectDependenciesWithExtendsClass(): void
    {
        /** @var ClassProvider $classProvider */
        $classProvider = $this->container->injectDependencies(new ClassProvider());

        $this->assertInstanceOf(ExtendsProvider::class, $classProvider);
        $this->assertInstanceOf(ClassProvider::class, $classProvider);
        $this->assertInstanceOf(FactoryProvider::class, $classProvider->getFactoryProvider());
    }

    public function testGetParameterClassName()
    {
        $reflectionParameter = new ReflectionParameter(self::REFLECTION_PARAMETERS, 'factoryProvider');

        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->getPrivateMethod('getParameterClassName', [$reflectionParameter]);

        $this->assertSame(FactoryProvider::class, $factoryProvider);
    }
}
