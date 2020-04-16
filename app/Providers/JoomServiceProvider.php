<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JoomServiceProvider extends ServiceProvider
{
    public function register()
    {
        require_once app_path() . '/Helpers/Joom/Links.php';
        require_once app_path() . '/Helpers/Joom/Forms.php';
    }

    public function boot()
    {
        
    }
}
