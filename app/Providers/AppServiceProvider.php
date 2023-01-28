<?php

namespace App\Providers;

use ErrorException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if (!cache('secret')) {
          try {
            $secret = file_get_contents(__DIR__.'/../../.secret');
          if (!$secret) {
            die(logger()->error('No secret found!'));
          } else {
            cache(['secret' => $secret]);
          }
          } catch(ErrorException) {
            die(logger()->error('Secret file not found!'));
          }
        }
        
    }
}
