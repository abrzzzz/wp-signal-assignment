<?php

namespace App\Domain\Event;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Contract\SignalListenerInterface;

class EventDispatcher
{
    private array $listeners = [];

    public function listen(string $name, SignalListenerInterface $listener)
    {
        $this->listeners[$name][] = $listener;
    }

    public function dispatch(SignalDispatchableInterface $event)
    {
        $eventClass = $event::class;
        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            $listener->handle($event);
        }
    }
}
