<?php

namespace SilahKosgei\Mpesa;

use Illuminate\Support\ServiceProvider;

class MpesaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mpesa.php' => config_path('mpesa.php'),
        ], 'mpesa-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mpesa.php', 'mpesa');

        $this->app->singleton('mpesa', function () {
            return new Mpesa;
        });
    }
}
