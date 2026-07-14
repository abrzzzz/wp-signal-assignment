<?php

namespace App\Domain\StateMachine;

use App\Domain\Entity\Signal;
use App\Domain\Event\EventDispatcher;
use App\Domain\Event\SignalTransitionFailed;
use App\Domain\Event\SignalTransitionSucceeded;

class SignalStateModifier
{
    private array $allowdTransitions = [
        SignalState::PENIDING->name => [SignalState::ACTIVE],
        SignalState::ACTIVE->name => [SignalState::HIT_TP, SignalState::HIT_SL, SignalState::CANCELED, SignalState::EXPIRED],
        SignalState::EXPIRED->name => [],
        SignalState::CANCELED->name => [],
        SignalState::HIT_TP->name => [],
        SignalState::HIT_SL->name => [],
    ];

    public function __construct(private Signal $signal, private EventDispatcher $dispatcher) {}

    public function transition(SignalState $newState): void
    {
        $currentState = $this->signal->getState();
        $allowed = $this->allowdTransitions[$currentState->name] ?? [];
        if (! in_array($newState, $allowed)) {
            // TODO Should dispatch and event
            $this->dispatcher->dispatch(new SignalTransitionFailed($this->signal));
            throw new \Exception('Transition from is not possible.');
        }

        $this->signal->setState($newState);
        $this->dispatcher->dispatch(new SignalTransitionSucceeded($this->signal));
    }
}
