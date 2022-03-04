<?php

namespace EscolaLms\Youtube\Tests;

use EscolaLms\Youtube\EscolaLmsYoutubeServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Settings\EscolaLmsSettingsServiceProvider;
use EscolaLms\Auth\Models\User;
use EscolaLms\Core\Tests\TestCase as CoreTestCase;
// use GuzzleHttp\Client;


class TestCase extends CoreTestCase
{
    use DatabaseTransactions;


    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {

        return [
            ...parent::getPackageProviders($app),
            EscolaLmsYoutubeServiceProvider::class,
            EscolaLmsSettingsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);

        $app['config']->set('jitsi.app_id', 'app_id');
        $app['config']->set('jitsi.secret', 'secret');
        $app['config']->set('jitsi.host', 'localhost');
    }
}
