<?php

namespace EscolaLms\Youtube\Tests\Services;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\AuthServiceContract;
use EscolaLms\Youtube\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

class YoutubeServiceTest extends TestCase
{
    use CreatesUsers;
    use WithFaker;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->makeAdmin();
        $this->mock->reset();
    }

    public function testGenerateYTAuthUrl()
    {
        $googleClient = $this->mock(AuthServiceContract::class);
        $googleClient->shouldReceive('getLoginUrl')->once()->andReturn('https://accounts.google.com/o/');

        $this->response = $this->actingAs($this->user, 'api')->json(
            'POST',
            'api/admin/g-token/generate',
            ['email' => $this->faker->email]
        );
        $this->response->assertOk();
        $this->response->assertJson(fn (AssertableJson $json) => $json->has('url')->etc());
        $content = json_decode($this->response->content());
        $this->assertTrue((bool)preg_match('/https:\/\/accounts.google.com.*/', $content->url));
    }

    public function testSetRefreshToken()
    {
        $token = [
            'refresh_token' => 'test'
        ];
        $googleClient = $this->mock(AuthenticateServiceContract::class);
        $googleClient->shouldReceive('getToken')->once()->andReturn($token);
        $this->response = $this->actingAs($this->user, 'api')->json(
            'GET',
            'api/refresh-token?code=' . md5(microtime())
        );
        $this->response->assertOk();
        $this->assertTrue(\Config::get('services.youtube.refresh_token') === $token['refresh_token']);
    }
}