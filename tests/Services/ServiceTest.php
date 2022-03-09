<?php

namespace EscolaLms\Youtube\Tests\Services;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class ServiceTest extends TestCase
{
    use CreatesUsers;
    use WithFaker;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->makeAdmin();
    }

    public function testYT()
    {
        $authObject = app(AuthenticateServiceContract::class);
        $token = $authObject->refreshToken(\Config::get('services.youtube.refresh_token'));
        $this->response = $authObject->authChannelWithCode($token);
        $this->assertTrue(
            isset($this->response['live_streaming_status'])
            && $this->response['live_streaming_status'] === 'enabled'
        );
    }

    public function testGenerateYTAuthUrl()
    {
        $this->response = $this->actingAs($this->user, 'api')->json(
            'POST',
            'api/admin/g-token/generate',
            ['email' => $this->faker->email]
        );
        $this->response->assertJson(fn (AssertableJson $json) => $json->has('url')->etc());
        $content = json_decode($this->response->content());
        $this->assertTrue((bool)preg_match('/https:\/\/accounts.google.com.*/', $content->url));
    }
}
