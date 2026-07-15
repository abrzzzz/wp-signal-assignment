<?php

namespace App\Domain\Contract;

use App\Domain\Entity\Signal;
use App\Domain\StateMachine\SignalState;

interface SignalRepositoryInterface
{
    public function getActives(): ?iterable;

    public function getById(int $id): ?Signal;

    public function getActiveByUserId(int $id): ?Signal;

    public function updateStatus(int $signalId, SignalState $state): void;

    public function save(Signal $signal): void;
}
