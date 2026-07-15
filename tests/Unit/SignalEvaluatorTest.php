<?php

use App\Domain\Entity\Signal;
use App\Domain\Service\SignalEvaluator;
use App\Domain\StateMachine\SignalState;

describe('Evaluate the signal', function () {

    it('should return Expired State', function () {

        $signal = new Signal(id: null, entryPrice: 10, stopLoss: 9, takeProfit: 15, expiry: 10238875, state: SignalState::PENIDING);
        $evaluator = new SignalEvaluator();
        $res = $evaluator->evaluate($signal, 13, 10238879);

        expect($res)->toBe(SignalState::EXPIRED);
    });

});
