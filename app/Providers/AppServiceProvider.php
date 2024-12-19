<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Model::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        Model::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        Model::deleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->save();
        });
    }
}
