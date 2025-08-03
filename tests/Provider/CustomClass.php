<?php

declare(strict_types=1);

namespace Tests\Provider;

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

    public function setMultiple(FactoryProvider $factoryProvider, string $str = 'test'): FactoryProvider
    {
        return $factoryProvider->setStr($str);
    }

    public function setDefaults(FactoryProvider $factoryProvider, string $str): FactoryProvider
    {
        return $factoryProvider->setStr($str);
    }
}
