<?php

namespace App\Domain\Contract;

interface SignalListenerInterface
{
    public function handle(SignalDispatchableInterface $event);
}
