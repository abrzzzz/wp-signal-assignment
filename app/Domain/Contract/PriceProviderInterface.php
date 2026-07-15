<?php

namespace App\Domain\Contract;

interface PriceProviderInterface
{
    public function fetch(): array;

    public function fetchByBase(string $base): int;
}
