<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\FactoryProvider;

class CustomClass
{
    private FactoryProvider $factoryProvider;

    public function getFactoryProvider(): FactoryProvider
    {
        return $this->factoryProvider;
    }

    /**
     * @required
     * */
    public function setFactoryProvider(FactoryProvider $factoryProvider): void
    {
        $this->factoryProvider = $factoryProvider;
    }

    public function setFactoryProviderSecond(FactoryProvider $factoryProvider): FactoryProvider
    {
        return $factoryProvider;
    }
}
