<?php

namespace App\Domain\Entity;

use App\Domain\StateMachine\SignalState;

class Signal
{
    public function __construct(
        private ?int $id,
        private float $entryPrice,
        private float $takeProfit,
        private float $stopLoss,
        private int $expiry,
        private SignalState $state,
    ) {
        if ($stopLoss >= $entryPrice || $entryPrice >= $takeProfit) {
            throw new \InvalidArgumentException('Invalid Pricing: Entry price must be either bigger that Stop Loss or less than Take Profit');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEntryPrice(): float
    {
        return $this->entryPrice;
    }

    public function getTakeProfit(): float
    {
        return $this->takeProfit;
    }

    public function getStopLoss(): float
    {
        return $this->stopLoss;
    }

    public function getExpiry(): int
    {
        return $this->expiry;
    }

    public function getState(): SignalState
    {
        return $this->state;
    }

    public function setState(SignalState $newState): void
    {
        $this->state = $newState;
    }
}
