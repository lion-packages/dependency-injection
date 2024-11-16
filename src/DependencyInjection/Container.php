<?php

declare(strict_types=1);

namespace Lion\Dependency\Injection;

use Closure;
use DI\Container as DIContainer;
use DI\ContainerBuilder;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

/**
 * Container to generate dependency injection
 *
 * @property DIContainer $container [Dependency Injection Container]
 *
 * @package Lion\DependencyInjection
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
     * Get files from a defined path
     *
     * @param string $folder [Defined route]
     *
     * @return array<string>
     */
    public function getFiles(string $folder): array
    {
        $files = [];

        $content = scandir($folder);

        foreach ($content as $element) {
            if ($element != '.' && $element != '..') {
                $path = $folder . '/' . $element;

                if (is_dir($path)) {
                    $files = array_merge($files, $this->getFiles($path));
                } else {
                    $files[] = realpath($path);
                }
            }
        }

        return $files;
    }

    /**
     * Gets the namespace of a class through a defined path
     *
     * @param string $file [File path]
     * @param string $namespace [Namespace for the file]
     * @param string $split [Separator to obtain the namespace]
     *
     * @return string
     */
    public function getNamespace(string $file, string $namespace, string $split = '/'): string
    {
        $splitFile = explode($split, $file);

        $namespace = str_replace("/", "\\", "{$namespace}{$splitFile[1]}");

        $namespace = str_replace('.php', '',  $namespace);

        return trim($namespace);
    }

    /**
     * Gets the parameters of a function
     *
     * @param ReflectionFunctionAbstract $method [Method obtained]
     * @param array $params [Array of defined parameters]
     *
     * @return array
     *
     * @throws ReflectionException
     */
    private function getParameters(ReflectionFunctionAbstract $method, array $params = []): array
    {
        $args = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                if (!empty($params[$parameter->getName()])) {
                    $args[] = $params[$parameter->getName()];
                } else {
                    $args[] = $parameter->getDefaultValue();
                }
            } else {
                if (!empty($params[$parameter->getName()])) {
                    $args[] = $params[$parameter->getName()];
                } else {
                    $args[] = $this->injectDependencies(
                        $this->container->get($this->getParameterClassName($parameter))
                    );
                }
            }
        }

        return $args;
    }

    /**
     * Inject dependencies into a method of a class
     *
     * @param object $object [Class object]
     * @param string $method [Method name]
     * @param array $params [Array of defined parameters]
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function injectDependenciesMethod(object $object, string $method, array $params = []): mixed
    {
        $method = (new ReflectionClass($object))->getMethod($method);

        return $method->invoke($object, ...$this->getParameters($method, $params));
    }

    /**
     * Inject dependencies to a callback
     *
     * @param Closure $closure [Defined callback]
     * @param array $params [Array of defined parameters]
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function injectDependenciesCallback(Closure $closure, array $params = []): mixed
    {
        $method = new ReflectionFunction($closure);

        return $method->invoke(...$this->getParameters($method, $params));
    }

    /**
     * Inject dependencies to methods of a class that have the annotation
     * 'required'
     *
     * @param object $object [Class object]
     * @param array $params [Array of defined parameters]
     *
     * @return object
     *
     * @throws ReflectionException
     */
    public function injectDependencies(object $object, array $params = []): object
    {
        foreach ((new ReflectionClass($object))->getMethods() as $method) {
            $docDocument = $method->getDocComment();

            if (is_string($docDocument)) {
                if (str_contains($docDocument, '@required')) {
                    $method->invoke($object, ...$this->getParameters($method, $params));
                }
            }
        }

        return $object;
    }

    /**
     * Gets the data type of the parameters obtained
     *
     * @param ReflectionParameter $parameter [Defined parameter of type
     * ReflectionParameter]
     *
     * @return null|string
     */
    private function getParameterClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        return $type ? (string) $type : null;
    }
}
