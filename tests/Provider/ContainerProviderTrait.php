<?php

declare(strict_types=1);

namespace Tests\Provider;

trait ContainerProviderTrait
{
    public static function normalizePathProvider(): array
    {
        return [
            [
                'path' => './Provider/CustomClass.php',
                'return' => './Provider/CustomClass.php'
            ],
            [
                'path' => '.\Provider\CustomClass.php',
                'return' => './Provider/CustomClass.php'
            ]
        ];
    }
}
