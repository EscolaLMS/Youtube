<?php

namespace EscolaLms\Youtube\Tests;

use EscolaLms\Youtube\EscolaLmsYoutubeServiceProvider;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Settings\EscolaLmsSettingsServiceProvider;
use EscolaLms\Core\Tests\TestCase as CoreTestCase;
use GuzzleHttp\Psr7\Response;
use EscolaLms\Core\Models\User;


class TestCase extends CoreTestCase
{
    use DatabaseTransactions;
    protected MockHandler $mock;


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

        $this->mock = new MockHandler([new Response(200, ['Token' => 'Token'], 'Hello, World'),]);
        $handlerStack = HandlerStack::create($this->mock);
    }
}
