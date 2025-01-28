<?php

declare(strict_types=1);

namespace Tests;

use DI\Container as DIContainer;
use DI\DependencyException;
use DI\NotFoundException;
use Lion\Dependency\Injection\Container;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;
use Tests\Provider\ClassConstructorProvider;
use Tests\Provider\ClassProvider;
use Tests\Provider\CustomClass;
use Tests\Provider\FactoryProvider;

class ContainerTest extends Test
{
    private const string STR = 'test';
    private Container $container;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->container = new Container();

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

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Testing]
    public function resolve(): void
    {
        /** @var ClassConstructorProvider $classProvider */
        $classProvider = $this->container->resolve(ClassConstructorProvider::class);

        $this->assertInstanceOf(ClassConstructorProvider::class, $classProvider);

        $returnObject = $classProvider->getCustomClass();

        $this->assertInstanceOf(CustomClass::class, $returnObject);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Testing]
    public function callMethod(): void
    {
        /** @var ClassProvider $classProvider */
        $classProvider = $this->container->resolve(ClassProvider::class);

        $this->assertInstanceOf(ClassProvider::class, $classProvider);

        $returnObject = $this->container->callMethod($classProvider, 'setSubClassProvider');

        $this->assertInstanceOf(ClassProvider::class, $returnObject);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    #[Testing]
    public function callMethodWithParams(): void
    {
        /** @var CustomClass $classProvider */
        $classProvider = $this->container->resolve(CustomClass::class);
        $this->assertInstanceOf(CustomClass::class, $classProvider);

        $returnObject = $this->container->callMethod($classProvider, 'setDefaults', [
            'str' => self::STR,
        ]);

        $this->assertIsObject($returnObject);
        $this->assertInstanceOf(FactoryProvider::class, $returnObject);
        $this->assertSame(self::STR, $returnObject->getStr());
    }

    #[Testing]
    public function callCallback(): void
    {
        $returnObject = $this->container->callCallback(function (
            CustomClass $customClass,
            FactoryProvider $factoryProvider
        ): FactoryProvider {
            return $customClass->setDefaults($factoryProvider, self::STR);
        });

        $this->assertIsObject($returnObject);
        $this->assertInstanceOf(FactoryProvider::class, $returnObject);
        $this->assertSame(self::STR, $returnObject->getStr());
    }

    #[Testing]
    public function callCallbackWithParams(): void
    {
        $returnObject = $this->container->callCallback(function (
            CustomClass $customClass,
            FactoryProvider $factoryProvider,
            string $str
        ): FactoryProvider {
            return $customClass->setDefaults($factoryProvider, $str);
        }, [
            'str' => self::STR,
        ]);

        $this->assertIsObject($returnObject);
        $this->assertInstanceOf(FactoryProvider::class, $returnObject);
        $this->assertSame(self::STR, $returnObject->getStr());
    }
}
