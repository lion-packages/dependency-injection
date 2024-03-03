<?php

declare(strict_types=1);

namespace Lion\DependencyInjection;

use Closure;
use DI\Container as DIContainer;
use DI\ContainerBuilder;
use Lion\Helpers\Str;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

/**
 * Container to generate dependency injection
 *
 * @package Lion\DependencyInjection
 */
class Container
{
    /**
     * [Object of class DIContainer]
     *
     * @var DIContainer $container
     */
    private DIContainer $container;

    /**
     * [Object of class Str]
     *
     * @var Str $str
     */
    private Str $str;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->container = (new ContainerBuilder())->useAutowiring(true)->useAttributes(true)->build();
        $this->str = new Str();
    }

    /**
     * Normalize routes depending on OS type
     *
     * @param  string $path [Defined route]
     *
     * @return string
     */
    public function normalizePath(string $path): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = str_replace('/', '\\', $path);
            $path = str_replace("\\\\", "\\", $path);
        } else {
            $path = str_replace('\\', '/', $path);
            $path = str_replace('//', '/', $path);
        }

        return $path;
    }

    /**
     * Get files from a defined path
     *
     * @param  string $folder [Defined route]
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
                    $files[] = $this->normalizePath($path);
                }
            }
        }

        return $files;
    }

    /**
     * Gets the namespace of a class through a defined path
     *
     * @param  string $file [File path]
     * @param  string $namespace [Namespace for the file]
     * @param  string $split [Separator to obtain the namespace]
     *
     * @return string
     */
    public function getNamespace(string $file, string $namespace, string $split = '/'): string
    {
        $splitFile = explode($split, $file);

        return $this->str->of("{$namespace}{$splitFile[1]}")->replace("/", "\\")->replace('.php', '')->trim()->get();
    }

    /**
     * Gets the parameters of a function
     *
     * @param  ReflectionFunctionAbstract $method [Method obtained]
     * @param  array $params [Array of defined parameters]
     *
     * @return array
     */
    private function getParameters(ReflectionFunctionAbstract $method, array $params = []): array
    {
        $args = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
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
     * @param  object $object [Class object]
     * @param  string $method [Method name]
     * @param  array $params [Array of defined parameters]
     *
     * @return mixed
     */
    public function injectDependenciesMethod(object $object, string $method, array $params = []): mixed
    {
        $method = (new ReflectionClass($object))->getMethod($method);

        return $method->invoke($object, ...$this->getParameters($method, $params));
    }

    /**
     * Inject dependencies to a callback
     *
     * @param  Closure $closure [Defined callback]
     * @param  array $params [Array of defined parameters]
     *
     * @return mixed
     */
    public function injectDependenciesCallback(Closure $closure, array $params = []): mixed
    {
        $method = new ReflectionFunction($closure);

        return $method->invoke(...$this->getParameters($method, $params));
    }

    /**
     * Inject dependencies to methods of a class that have the annotation
     * '@required'
     *
     * @param  object $object [Class object]
     * @param  array $params [Array of defined parameters]
     *
     * @return object
     */
    public function injectDependencies(object $object, array $params = []): object
    {
        foreach ((new ReflectionClass($object))->getMethods() as $method) {
            $docDocument = $method->getDocComment();

            if (is_string($docDocument)) {
                if ((bool) preg_match('/@required/', $docDocument)) {
                    $method->invoke($object, ...$this->getParameters($method, $params));
                }
            }
        }

        return $object;
    }

    /**
     * Gets the data type of the parameters obtained
     *
     * @param  ReflectionParameter $parameter [Defined parameter of type
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
