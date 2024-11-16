<?php

declare(strict_types=1);

namespace Lion\Dependency\Injection;

use DI\Container as DIContainer;
use DI\ContainerBuilder;
use Exception;

/**
 * Dependency Injection Container Wrapper
 *
 * This class simplifies dependency injection by leveraging PHP-DI
 *
 * It provides methods to resolve dependencies, invoke methods,
 * and execute callbacks with automatic dependency injection
 *
 * @property DIContainer $container [Dependency Injection Container]
 *
 * @package Lion\Dependency\Injection
 */
class Container
{
    /**
     * [Dependency Injection Container]
     *
     * @var DIContainer $container
     */
    private DIContainer $container;

    /**
     * Class constructor
     *
     * Initializes the PHP-DI container with autowiring and attribute support
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->container = (new ContainerBuilder())
            ->useAutowiring(true)
            ->useAttributes(true)
            ->build();
    }

    /**
     * Resolves a class or dependency from the container
     *
     * @param string $className [The fully qualified class name to resolve]
     *
     * @return object
     */
    public function resolve(string $className): object
    {
        return $this->container->get($className);
    }

    /**
     * Calls a method on an object with automatic dependency injection.
     *
     * @param object $object [The object instance]
     * @param string $method [The method name to invoke]
     * @param array $params [Optional array of additional parameters to pass]
     *
     * @return mixed
     */
    public function callMethod(object $object, string $method, array $params = []): mixed
    {
        return $this->container->call([$object, $method], $params);
    }

    /**
     * Executes a callback with automatic dependency injection
     *
     * @param callable $callback [The callback to execute]
     * @param array $params [Optional array of additional parameters to pass]
     *
     * @return mixed
     */
    public function callCallback(callable $callback, array $params = []): mixed
    {
        return $this->container->call($callback, $params);
    }
}
