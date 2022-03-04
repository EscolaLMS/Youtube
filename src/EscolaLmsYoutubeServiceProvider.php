<?php

namespace EscolaLms\Youtube;

use EscolaLms\Youtube\Services\AuthenticateService;
use EscolaLms\Youtube\Services\AuthService;
use EscolaLms\Youtube\Services\ChannelService;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\AuthServiceContract;
use EscolaLms\Youtube\Services\Contracts\ChannelServiceContract;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use EscolaLms\Youtube\Services\Contracts\VideoServiceContract;
use EscolaLms\Youtube\Services\LiveStreamService;
use EscolaLms\Youtube\Services\VideoService;
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
        ChannelServiceContract::class => ChannelService::class,
        LiveStreamServiceContract::class => LiveStreamService::class,
        VideoServiceContract::class => VideoService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }


    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/youtube.php',
            'youtube'
        );
    }
}
