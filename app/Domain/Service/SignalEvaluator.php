<?php

namespace App\Domain\Service;

use App\Domain\Entity\Signal;
use App\Domain\StateMachine\SignalState;

class SignalEvaluator
{
    public function evaluate(Signal $signal, int $currentPrice, int $currentTime): SignalState
    {
        if ($currentTime > $signal->getExpiry()) {
            return SignalState::EXPIRED;
        }

        if ($currentPrice >= $signal->getTakeProfit()) {
            return SignalState::HIT_TP;
        }

        if ($currentPrice <= $signal->getStopLoss()) {
            return SignalState::HIT_SL;
        }

        if ($signal->getState() == SignalState::PENIDING) {
            return SignalState::ACTIVE;
        }

        return $signal->getState();
    }
}
