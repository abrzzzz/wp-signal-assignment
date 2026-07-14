<?php

namespace App\Domain\Event;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Entity\Signal;

class SignalTransitionFailed implements SignalDispatchableInterface
{
    public function __construct(private Signal $signal) {}

    public function getSignal(): Signal
    {
        return $this->signal;
    }
}
