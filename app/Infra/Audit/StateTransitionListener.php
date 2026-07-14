<?php

namespace App\Infra\Audit;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Contract\SignalListenerInterface;

class StateTransitionListener implements SignalListenerInterface
{
    public function handle(SignalDispatchableInterface $event)
    {
        // TODO
        // save autit into the db
    }
}
