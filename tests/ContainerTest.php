<?php

declare(strict_types=1);

namespace Tests;

use DI\Container as DIContainer;
use Lion\Dependency\Injection\Container;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Tests\Provider\ClassProvider;
use Tests\Provider\CustomClass;
use Tests\Provider\ExtendsProvider;
use Tests\Provider\FactoryProvider;

class ContainerTest extends Test
{
    private const string STR = 'test';
    private const string DEFAULT_VALUE = 'default-value';
    private const string FOLDER = './tests/';
    private const string PATH_FILE = './Provider/CustomClass.php';
    private const array FILES = [
        '/var/www/html/tests/Provider/ClassProvider.php',
        '/var/www/html/tests/Provider/CustomClass.php',
        '/var/www/html/tests/Provider/ExtendsProvider.php',
        '/var/www/html/tests/Provider/FactoryProvider.php',
        '/var/www/html/tests/Provider/SubClassProvider.php'
    ];
    private const array REFLECTION_PARAMETERS = [CustomClass::class, 'setFactoryProvider'];

    private Container $container;
    private CustomClass $customClass;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->container = new Container();
        $this->customClass = new CustomClass();

        $this->initReflection($this->container);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function constructTest(): void
    {
        $this->assertInstanceOf(DIContainer::class, $this->getPrivateProperty('container'));
    }

    #[Testing]
    public function getFiles(): void
    {
        $files = $this->container->getFiles(self::FOLDER . 'Provider/');

        $this->assertIsArray($files);
        $this->assertSame(self::FILES, $files);
    }

    #[Testing]
    public function getNamespace(): void
    {
        $namespace = $this->container->getNamespace(self::PATH_FILE, 'Tests\\Provider\\', 'Provider/');

        $this->assertIsString($namespace);
        $this->assertSame(CustomClass::class, $namespace);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function getParameters(): void
    {
        $parameters = $this->getPrivateMethod(
            'getParameters',
            [new ReflectionMethod(new CustomClass(), 'setFactoryProvider')]
        );

        $this->assertIsArray($parameters);
        $this->assertInstanceOf(FactoryProvider::class, reset($parameters));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function getParametersWithDefaultValue(): void
    {
        $parameters = $this->getPrivateMethod(
            'getParameters',
            [new ReflectionMethod(new CustomClass(), 'setMultiple')]
        );

        $this->assertIsArray($parameters);

        $second = end($parameters);

        $this->assertInstanceOf(FactoryProvider::class, reset($parameters));
        $this->assertIsString($second);
        $this->assertSame(self::STR, $second);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function getParametersWithDefaultDeclaredValue(): void
    {
        $parameters = $this->getPrivateMethod(
            'getParameters',
            [
                new ReflectionMethod(new CustomClass(), 'setMultiple'),
                ['str' => self::DEFAULT_VALUE]
            ]
        );

        $this->assertIsArray($parameters);

        $second = end($parameters);

        $this->assertInstanceOf(FactoryProvider::class, reset($parameters));
        $this->assertIsString($second);
        $this->assertSame(self::DEFAULT_VALUE, $second);
    }

    #[Testing]
    public function injectDependenciesMethod(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesMethod($this->customClass, 'setFactoryProviderSecond');

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
    }

    #[Testing]
    public function injectDependenciesMethodWithMultipleArguments(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesMethod($this->customClass, 'setMultiple');

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
        $this->assertSame(self::STR, $factoryProvider->getStr());
    }

    #[Testing]
    public function injectDependenciesMethodWithMultipleArgumentsDefault(): void
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

    #[Testing]
    public function injectDependenciesCallback(): void
    {
        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->container->injectDependenciesCallback(
            fn (FactoryProvider $factoryProvider, string $str): FactoryProvider => $factoryProvider->setStr($str),
            ['str' => self::STR]
        );

        $this->assertInstanceOf(FactoryProvider::class, $factoryProvider);
        $this->assertSame(self::STR, $factoryProvider->getStr());
    }

    #[Testing]
    public function injectDependencies(): void
    {
        /** @var CustomClass $customClass */
        $customClass = $this->container->injectDependencies($this->customClass);

        $this->assertInstanceOf(CustomClass::class, $customClass);
        $this->assertInstanceOf(FactoryProvider::class, $customClass->getFactoryProvider());
    }

    #[Testing]
    public function injectDependenciesWithExtendsClass(): void
    {
        /** @var ClassProvider $classProvider */
        $classProvider = $this->container->injectDependencies(new ClassProvider());

        $this->assertInstanceOf(ExtendsProvider::class, $classProvider);
        $this->assertInstanceOf(ClassProvider::class, $classProvider);
        $this->assertInstanceOf(FactoryProvider::class, $classProvider->getFactoryProvider());
    }

    #[Testing]
    public function injectDependenciesWithSubDependencies(): void
    {
        /** @var ClassProvider $classProvider */
        $classProvider = $this->container->injectDependencies(new ClassProvider);

        $str = $classProvider
            ->getSubClassProvider()
            ->getExtendsProvider()
            ->getFactoryProviderExtends()
            ->setStr(self::STR)
            ->getStr();

        $this->assertIsString($str);
        $this->assertSame(self::STR, $str);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function getParameterClassName()
    {
        $reflectionParameter = new ReflectionParameter(self::REFLECTION_PARAMETERS, 'factoryProvider');

        /** @var FactoryProvider $factoryProvider */
        $factoryProvider = $this->getPrivateMethod('getParameterClassName', [$reflectionParameter]);

        $this->assertSame(FactoryProvider::class, $factoryProvider);
    }
}
