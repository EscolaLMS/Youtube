<?php

namespace EscolaLms\Youtube\Tests\Services;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Events\YtProblem;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use EscolaLms\Youtube\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

class YoutubeServiceTest extends TestCase
{
    use CreatesUsers;
    use WithFaker;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        config([
            'services.youtube.client_id' => 'test_client_id',
            'services.youtube.client_secret' => 'test_secret',
            'services.youtube.api_key' => 'test_api_key',
            'services.youtube.refresh_token' => 'test_refresh_token',
            'services.youtube.redirect_url' => 'redirect_url',
        ]);

        $this->user = $this->makeAdmin();
        $this->mock->reset();
    }

    public function testGenerateYTAuthUrl()
    {
        $googleClient = $this->mock(YoutubeServiceContract::class);
        $googleClient->shouldReceive('generateYTAuthUrl')->once()->andReturn('https://accounts.google.com/o/');

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

    public function testVerifyReportYtFail()
    {
        Event::fake();
        \Config::set('services.youtube.email', $this->faker->email);
        $ytServiceContract = app(YoutubeServiceContract::class);
        try {
            $ytServiceContract->generateYTStream(new YTBroadcastDto());
        } catch (\Exception $ex) {
        }
        Event::assertDispatched(YtProblem::class);
    }
}
