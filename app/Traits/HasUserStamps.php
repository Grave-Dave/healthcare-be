<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasUserStamps
{
    protected static function bootHasHasUserStamps(): void
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save();
        });
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
