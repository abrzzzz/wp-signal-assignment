<?php

namespace App\Domain\Contract;

interface PriceProviderInterface
{
    public function fetch(string $base): int;
}
