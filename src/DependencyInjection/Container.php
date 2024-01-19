<?php

declare(strict_types=1);

namespace Lion\DependencyInjection;

use DI\Container as DIContainer;
use DI\ContainerBuilder;
use Lion\Helpers\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Container
{
    private DIContainer $container;
    private Str $str;

    public function __construct()
    {
        $this->container = (new ContainerBuilder())->useAutowiring(true)->useAttributes(true)->build();
        $this->str = new Str();
    }

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

    public function getNamespace(string $file, string $namespace, string $split): string
    {
        $splitFile = explode($split, $file);

        return $this->str->of("{$namespace}{$splitFile[1]}")->replace("/", "\\")->replace('.php', '')->trim()->get();
    }

    private function getParameters(ReflectionMethod $method): array
    {
        return array_map(
            fn($parameter) => $this->container->get($this->getParameterClassName($parameter)),
            $method->getParameters()
        );
    }

    public function injectDependencies(object $object): object
    {
        $reflectionClass = new ReflectionClass($object);

        foreach ($reflectionClass->getMethods() as $method) {
            $docDocument = $method->getDocComment();

            if (is_string($docDocument)) {
                if ((bool) preg_match('/@required/', $docDocument)) {
                    $method->invoke($object, ...$this->getParameters($method));
                }
            }
        }

        return $object;
    }

    private function getParameterClassName(ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        return $type ? (string) $type : null;
    }
}
