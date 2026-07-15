<?php

namespace App\Domain\Contract;

use App\Domain\Entity\Signal;

interface SignalRepositoryInterface
{
    public function getActives(): ?iterable;

    public function getById(int $id): ?Signal;

    public function getActiveByUserId(int $id): ?Signal;

    public function save(Signal $signal): void;
}
