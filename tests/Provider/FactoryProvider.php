<?php

declare(strict_types=1);

namespace Tests\Provider;

class FactoryProvider
{
    private string $str;

    public function getStr(): string
    {
        return $this->str;
    }

    public function setStr(string $str): FactoryProvider
    {
        $this->str = $str;

        return $this;
    }
}
