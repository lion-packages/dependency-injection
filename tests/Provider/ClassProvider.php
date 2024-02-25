<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\ExtendsProvider;

class ClassProvider extends ExtendsProvider
{
    public function getFactoryProvider(): FactoryProvider
    {
        return parent::getFactoryProvider();
    }
}
