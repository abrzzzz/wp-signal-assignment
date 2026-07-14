<?php

use App\Domain\Entity\Signal;
use App\Domain\StateMachine\SignalState;
use App\Domain\StateMachine\SignalStateModifier;

describe('Allowed State', function () {

    it('should Allow Pending To Active', function () {
        $signal = new Signal(id: null, entryPrice: 10, stopLoss: 9, takeProfit: 11, expiry: 10238875, state: SignalState::PENIDING);
        $modifier = new SignalStateModifier($signal);
        $modifier->transition(SignalState::ACTIVE);

        expect($signal->getState())->toBe(SignalState::ACTIVE);

    });

});

describe('Not Allowd State', function () {

    it('Should not Allow Pending to Take Profit state', function () {
        $signal = new Signal(id: null, entryPrice: 10, stopLoss: 9, takeProfit: 11, expiry: 10238875, state: SignalState::PENIDING);
        $modifier = new SignalStateModifier($signal);
        $modifier->transition(SignalState::HIT_TP);

        expect($signal->getState())->toBe(SignalState::ACTIVE);

    })->throws(Exception::class);

});
