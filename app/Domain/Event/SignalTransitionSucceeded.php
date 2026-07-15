<?php

namespace App\Domain\Event;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Entity\Signal;

class SignalTransitionSucceeded implements SignalDispatchableInterface
{
    public function __construct(public Signal $signal, public ?array $data = null) {}
}
