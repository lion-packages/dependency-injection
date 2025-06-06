<?php

declare(strict_types=1);

namespace Lion\Dependency\Injection;

use DI\Container as DIContainer;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

/**
 * Dependency Injection Container Wrapper
 *
 * This class simplifies dependency injection by leveraging PHP-DI
 *
 * It provides methods to resolve dependencies, invoke methods,
 * and execute callbacks with automatic dependency injection
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
     * Class constructor, Initializes the PHP-DI container with autowiring and
     * attribute support
     *
     * @throws Exception
     *
     * @infection-ignore-all
     */
    public function __construct()
    {
        $this->container = new ContainerBuilder()
            ->useAutowiring(true)
            ->useAttributes(true)
            ->build();
    }

    /**
     * Resolves a class or dependency from the container
     *
     * @param string $className [The fully qualified class name to resolve]
     *
     * @return mixed
     *
     * @throws DependencyException [Error while resolving the entry]
     * @throws NotFoundException [No entry found for the given name]
     */
    public function resolve(string $className): mixed
    {
        return $this->container->get($className);
    }

    /**
     * Calls a method on an object with automatic dependency injection.
     *
     * @param mixed $object [The object instance]
     * @param string $method [The method name to invoke]
     * @param array<string, mixed> $params [Optional array of additional
     * parameters to pass]
     *
     * @return mixed
     */
    public function callMethod(mixed $object, string $method, array $params = []): mixed
    {
        return $this->container->call([$object, $method], $params);
    }

    /**
     * Executes a callback with automatic dependency injection
     *
     * @param callable $callback [The callback to execute]
     * @param array<string, mixed> $params [Optional array of additional
     * parameters to pass]
     *
     * @return mixed
     */
    public function callCallback(callable $callback, array $params = []): mixed
    {
        return $this->container->call($callback, $params);
    }
}
