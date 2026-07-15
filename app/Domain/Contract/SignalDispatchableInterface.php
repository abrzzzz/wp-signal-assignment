<?php

namespace App\Domain\Contract;

use App\Domain\Entity\Signal;
use App\Domain\StateMachine\SignalState;

interface SignalDispatchableInterface
{
    public function __construct(Signal $signal, SignalState $from_state, SignalState $to_state);
}
