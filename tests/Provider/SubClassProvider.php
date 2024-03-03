<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\ExtendsProvider;

class SubClassProvider
{
    private ExtendsProvider $extendsProvider;

    /**
     * @required
     */
    public function setExtendsProvider(ExtendsProvider $extendsProvider): SubClassProvider
    {
        $this->extendsProvider = $extendsProvider;

        return $this;
    }

    public function getExtendsProvider(): ExtendsProvider
    {
        return $this->extendsProvider;
    }
}
