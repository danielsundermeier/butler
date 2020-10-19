<?php

namespace {{ vendor_namespace }}\{{ name_namespace }};

use Illuminate\Support\ServiceProvider;

class {{ name_namespace }}ServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', '{{ vendor }}');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', '{{ vendor }}');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/{{ name }}.php', '{{ name }}');

        // Register the service the package provides.
        // $this->app->singleton('{{ name }}', function ($app) {
        //     return new {{ name_namespace }};
        // });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['{{ name }}'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/{{ name }}.php' => config_path('{{ name }}.php'),
        ], '{{ name }}.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/{{ vendor }}'),
        ], '{{ name }}.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/{{ vendor }}'),
        ], '{{ name }}.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/{{ vendor }}'),
        ], '{{ name }}.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
