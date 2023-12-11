<?php
namespace RecurLoop\Recaptcha\Providers;

use Illuminate\Support\ServiceProvider;
use RecurLoop\Recaptcha\Recaptcha;

class RecaptchaServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/recaptcha.php' => config_path('recaptcha.php'),
            ], 'config');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/recaptcha.php', 'recaptcha');

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(Recaptcha::class);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Recaptcha::class];
    }

}
