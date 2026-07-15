<?php

namespace App\Infra\Api;

use AlgoYounes\CircuitBreaker\Managers\CircuitManager;
use AlgoYounes\CircuitBreaker\ValueObjects\CircuitResult;
use AlgoYounes\CircuitBreaker\ValueObjects\CircuitTransition;
use App\Domain\Contract\PriceProviderInterface;
use Illuminate\Support\Facades\Http;

class TabdealPriceProvider implements PriceProviderInterface
{
    protected const URL = 'https://api-web.tabdeal.org/r/plots/currencies/dynamic-info/';

    protected const CACHE_KEY = 'last_tabdeal_api_response';

    private $circuit;

    public function __construct()
    {
        $circuitManager = app(CircuitManager::class);
        $this->circuit = $circuitManager->forService('tabdeal-api');

        $this->circuit->onOpen(function (CircuitTransition $transition) {
            error_log('[Signal][WARNING] tabdeal-api service circuit opened, state: %s', $transition->getNewState());
        });

        $this->circuit->onSuccess(function (CircuitResult $result, CircuitTransition $transition) {
            // Called when a wrapped call succeeds
        });

        $this->circuit->onFailure(function (CircuitResult $result, CircuitTransition $transition) {
            // Called when a wrapped call fails
        });

    }

    public function fetchByBase(string $base): int
    {

        $res = $this->circuit->run(
            function () {
                $response = Http::timeout(3)->retry(3)->get(self::URL);
                if (! $response->successful()) {
                    throw new \Exception();
                }

                set_transient(self::CACHE_KEY, $response->json(), DAY_IN_SECONDS);

                return $response->json();
            },
            function () {
                return get_transient(self::CACHE_KEY);
            },
        );

        return (int) $res->result['currencies'][$base]['IRT']['price'];
    }

    public function fetch(): array
    {

        $res = $this->circuit->run(
            function () {
                $response = Http::timeout(3)->retry(3)->get(self::URL);
                if (! $response->successful()) {
                    throw new \Exception();
                }

                set_transient(self::CACHE_KEY, $response->json(), DAY_IN_SECONDS);

                return $response->json();
            },
            function () {
                return get_transient(self::CACHE_KEY);
            },
        );

        return (array) $res->result['currencies'];
    }
}
