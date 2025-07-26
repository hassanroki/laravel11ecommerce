<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CartConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // booted() ensures that all services (like DB, files, etc.) are available
        $this->app->booted(function () {
            if (Schema::hasTable('tax_settings')) {
                $taxRate = DB::table('tax_settings')->value('tax_rate') ?? 0;
                config(['cart.tax' => $taxRate]);
            }
        });
    }
}
