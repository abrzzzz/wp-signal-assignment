<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Signal extends Post
{
    protected static function booted(): void
    {
        static::addGlobalScope('signal_post_type', function (Builder $builder) {
            $builder->where('post_type', 'signal');
        });
    }

    #[Scope]
    protected function publish(Builder $builder): void
    {
        $builder->where('post_status', 'publish');
    }
}
