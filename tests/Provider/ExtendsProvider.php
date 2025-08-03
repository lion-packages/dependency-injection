<?php

declare(strict_types=1);

namespace Tests\Provider;

class ExtendsProvider
{
    private FactoryProvider $factoryProvider;

    /**
     * @required
     * */
    public function setFactoryProvider(FactoryProvider $factoryProvider): void
    {
        $this->factoryProvider = $factoryProvider;
    }

    public function getFactoryProviderExtends(): FactoryProvider
    {
        return $this->factoryProvider;
    }

    protected function getFactoryProvider(): FactoryProvider
    {
        return $this->factoryProvider;
    }
}
