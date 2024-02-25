<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\FactoryProvider;

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

    protected function getFactoryProvider(): FactoryProvider
    {
        return $this->factoryProvider;
    }
}
