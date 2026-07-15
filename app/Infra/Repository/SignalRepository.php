<?php

namespace App\Infra\Repository;

use App\Domain\Contract\SignalRepositoryInterface;
use App\Domain\Entity\Signal as AppSignal;
use App\Domain\StateMachine\SignalState;
use App\Infra\WP\PostMetaManager;
use App\Models\Signal;

class SignalRepository implements SignalRepositoryInterface
{
    protected Signal $model;

    public function __construct()
    {
        $this->model = new Signal();
    }

    public function getActives(): ?iterable
    {
        $res = $this->model->publish()->with(['meta' => function ($query) {
            $query->whereIn('meta_key', [
                PostMetaManager::META_BASE_QUOTE,
                PostMetaManager::META_ENTRY_PRICE,
                PostMetaManager::META_STOP_LOSS,
                PostMetaManager::META_TAKE_PROFIT,
                PostMetaManager::META_EXPIRATION,
                PostMetaManager::META_STATUS,
            ]);
        }])->get();
        $mapped = $res->map(fn($record) => $this->mapToSignal($record));

        return $mapped;
    }

    public function getById(int $id): ?AppSignal
    {
        $res = $this->model->with(['meta' => function ($query) {
            $query->whereIn('meta_key', [
                PostMetaManager::META_BASE_QUOTE,
                PostMetaManager::META_ENTRY_PRICE,
                PostMetaManager::META_STOP_LOSS,
                PostMetaManager::META_TAKE_PROFIT,
                PostMetaManager::META_EXPIRATION,
                PostMetaManager::META_STATUS,
            ]);
        }])->find($id);

        return $this->mapToSignal($res);
    }

    public function getActiveByUserId(int $user_id): ?AppSignal
    {
        $res = $this->model->publish()->where('post_author', $user_id)->with(['meta' => function ($query) {
            $query->whereIn('meta_key', [
                PostMetaManager::META_BASE_QUOTE,
                PostMetaManager::META_ENTRY_PRICE,
                PostMetaManager::META_STOP_LOSS,
                PostMetaManager::META_TAKE_PROFIT,
                PostMetaManager::META_EXPIRATION,
                PostMetaManager::META_STATUS,
            ]);
        }])->first();

        return $this->mapToSignal($res);
    }

    public function checkActiveByUserId(int $user_id): bool
    {
        return $this->model->publish()->where('post_author', $user_id)->exists();
    }

    public function save(AppSignal $signal): void
    {
        if ($signal->getId()) {
            $this->update($signal);
        }

        $this->insert();

    }

    public function updateStatus(int $signalId, SignalState $state): void
    {
        update_post_meta($signalId, PostMetaManager::META_STATUS, $state->name);
    }

    private function insert(AppSignal $signal)
    {
        $post = wp_insert_post([
            'post_type' => 'signal',
            'post_status' => 'publish',
        ]);

        update_post_meta($post->ID, PostMetaManager::META_BASE_QUOTE, $signal->getBaseQuote());
        update_post_meta($post->ID, PostMetaManager::META_TAKE_PROFIT, $signal->getTakeProfit());
        update_post_meta($post->ID, PostMetaManager::META_STOP_LOSS, $signal->getStopLoss());
        update_post_meta($post->ID, PostMetaManager::META_BASE_QUOTE, $signal->getBaseQuote());
        update_post_meta($post->ID, PostMetaManager::META_EXPIRATION, $signal->getExpiry());
        update_post_meta($post->ID, PostMetaManager::META_STATUS, $signal->getState());

    }

    private function update(AppSignal $signal)
    {
        update_post_meta($signal->getId(), PostMetaManager::META_BASE_QUOTE, $signal->getBaseQuote());
        update_post_meta($signal->getId(), PostMetaManager::META_TAKE_PROFIT, $signal->getTakeProfit());
        update_post_meta($signal->getId(), PostMetaManager::META_STOP_LOSS, $signal->getStopLoss());
        update_post_meta($signal->getId(), PostMetaManager::META_BASE_QUOTE, $signal->getBaseQuote());
        update_post_meta($signal->getId(), PostMetaManager::META_EXPIRATION, $signal->getExpiry());
        update_post_meta($signal->getId(), PostMetaManager::META_STATUS, $signal->getState());

    }

    private function mapToSignal($record)
    {

        $meta = $record->meta->mapWithKeys(fn($m) => [$m->meta_key => $m->meta_value]);

        $baseQuote = isset($meta['base_quote']) ? $meta['base_quote'] : null;
        $entryPrice = isset($meta['entry_price']) ? $meta['entry_price'] : null;
        $stopLoss = isset($meta['stop_loss']) ? $meta['stop_loss'] : null;
        $takeProfit = isset($meta['take_profit']) ? $meta['take_profit'] : null;
        $expiration = isset($meta['expiration']) ? $meta['expiration'] : null;
        $status = isset($meta['status']) ? $meta['status'] : null;
        $state = isset($meta['signal_status']) ? $meta['signal_status'] : null;

        return new AppSignal(
            id: $record->ID,
            baseQuote: $baseQuote,
            entryPrice: $entryPrice,
            stopLoss: $stopLoss,
            takeProfit: $takeProfit,
            expiry: strtotime($expiration),
            state: constant(SignalState::class . '::' . $state),
        );

    }
}
