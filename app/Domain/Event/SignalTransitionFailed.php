<?php

namespace App\Domain\Event;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Entity\Signal;
use App\Domain\StateMachine\SignalState;

class SignalTransitionFailed implements SignalDispatchableInterface
{
    public function __construct(public Signal $signal, public SignalState $from_state, public SignalState $to_state) {}
}
