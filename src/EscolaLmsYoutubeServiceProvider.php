<?php

namespace EscolaLms\Youtube;

use EscolaLms\Settings\EscolaLmsSettingsServiceProvider;
use EscolaLms\Core\EscolaLmsServiceProvider;
use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\Youtube\Services\AuthenticateService;
use EscolaLms\Youtube\Services\AuthService;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\AuthServiceContract;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use EscolaLms\Youtube\Services\LiveStreamService;
use EscolaLms\Youtube\Services\YoutubeService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */

class EscolaLmsYoutubeServiceProvider extends ServiceProvider
{

    const CONFIG_KEY = 'youtube';

    public $singletons = [
        AuthServiceContract::class => AuthService::class,
        AuthenticateServiceContract::class => AuthenticateService::class,
        LiveStreamServiceContract::class => LiveStreamService::class,
        YoutubeServiceContract::class => YoutubeService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/youtube.php',
            'youtube'
        );

        $this->app->register(EscolaLmsSettingsServiceProvider::class);
        $this->app->register(EscolaLmsServiceProvider::class);
        AdministrableConfig::registerConfig('services.youtube.refresh_token', ['nullable', 'string'], false);
        AdministrableConfig::registerConfig('services.youtube.client_id', ['nullable', 'string'], false);
        AdministrableConfig::registerConfig('services.youtube.client_secret', ['nullable', 'string'], false);
        AdministrableConfig::registerConfig('services.youtube.api_key', ['nullable', 'string'], false);
        AdministrableConfig::registerConfig('services.youtube.redirect_url', ['nullable', 'string'], false);
        Config::set('escola_settings.use_database', true);
    }
}
