<?php

namespace EscolaLms\Youtube\Tests\Services;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Tests\TestCase;

class ServiceTest extends TestCase
{
    use CreatesUsers;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->makeStudent();
        AdministrableConfig::setConfig([
            'services.youtube.refresh_token' => env('YT_REFRESH_TOKEN')
        ]);
    }

    public function testYT()
    {
        $authObject = app(AuthenticateServiceContract::class);
        $token = $authObject->refreshToken(AdministrableConfig::getConfig('services.youtube.refresh_token'));
        $this->response = $authObject->authChannelWithCode($token);
        $this->assertTrue(
            isset($this->response['live_streaming_status'])
            && $this->response['live_streaming_status'] === 'enabled'
        );
    }
}
