<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\ExtendsProvider;
use Tests\Provider\SubClassProvider;

class ClassProvider extends ExtendsProvider
{
    private SubClassProvider $subClassProvider;

    /**
     * @required
     */
    public function setSubClassProvider(SubClassProvider $subClassProvider): ClassProvider
    {
        $this->subClassProvider = $subClassProvider;

        return $this;
    }

    public function getSubClassProvider(): SubClassProvider
    {
        return $this->subClassProvider;
    }

    public function getFactoryProvider(): FactoryProvider
    {
        return parent::getFactoryProvider();
    }
}
