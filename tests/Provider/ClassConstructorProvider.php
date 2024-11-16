<?php

declare(strict_types=1);

namespace Tests\Provider;

use Tests\Provider\CustomClass;

class ClassConstructorProvider
{
    public function __construct(
        private CustomClass $customClass
    ) {}

    public function getCustomClass(): CustomClass
    {
        return $this->customClass;
    }
}
