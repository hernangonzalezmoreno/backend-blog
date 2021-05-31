<?php

namespace App\Providers;

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
        //Agregar esta regla personalizada de validacion
        \Validator::extend('alpha_spaces', function ($attribute, $value) {

            // Solo acepta alpha y espacios
            // Para ademas aceptar guiones se puede usar la siguiente: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s]+$/u', $value);

        });
    }
}
