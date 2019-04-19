<?php

namespace HayriCan\CodecFastSms;

use Illuminate\Support\ServiceProvider;

class CodecFastSmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (config('codecfastsms.record')){
            $this->loadMigrationsFrom(__DIR__.'/migrations');
        }

        $this->publishes([
            __DIR__.'/../config/codecfastsms.php' => config_path('codecfastsms.php'),
        ]);
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
    }

    public function register()
    {
        
    }
}