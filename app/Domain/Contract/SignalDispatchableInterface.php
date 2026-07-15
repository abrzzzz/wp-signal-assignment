<?php

namespace App\Domain\Contract;

use App\Domain\Entity\Signal;

interface SignalDispatchableInterface
{
    public function __construct(Signal $signal, ?array $data);
}
