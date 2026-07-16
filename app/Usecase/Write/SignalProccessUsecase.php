<?php

namespace App\Usecase\Write;

use App\Domain\Entity\Signal;
use App\Domain\Event\EventDispatcher;
use App\Domain\Service\SignalEvaluator;
use App\Domain\StateMachine\SignalStateModifier;
use App\Infra\Api\TabdealPriceProvider;
use App\Infra\Repository\SignalRepository;

class SignalProccessUsecase
{
    public function execute()
    {
        try {
            // fetch All Active Siganls
            $repo = new SignalRepository();
            $signals = $repo->getActives();
            $tabdeal = new TabdealPriceProvider();
            $prices = $tabdeal->fetch();
            $signalEvaluator = new SignalEvaluator();
            // proccess each
            $signals->each(function (Signal $signal) use ($signalEvaluator, $prices, $repo) {

                $newState = $signalEvaluator->evaluate($signal, $prices[$signal->getBaseQuote()]['IRT']['price'], time());
                if ($newState === $signal->getState()) {
                    return;
                }
                $stateModifier = new SignalStateModifier($signal, app(EventDispatcher::class));
                $updatedSignal = $stateModifier->transition($newState);
                $repo->updateStatus($updatedSignal->getId(), $updatedSignal->getState());
            });
        } catch (\Exception $e) {
            error_log('[signal][error]: ' . $e->getMessage());
        }
    }
}
