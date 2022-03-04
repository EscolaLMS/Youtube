<?php

namespace EscolaLms\Youtube\Tests\Services;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Jitsi\Services\Contracts\JitsiServiceContract;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use EscolaLms\Youtube\Tests\TestCase;

class ServiceTest extends TestCase
{

    use CreatesUsers;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->makeStudent();
    }

    private function decodeJWT($token)
    {
        return (json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1])))));
    }

    public function testYT()
    {
        // mb2w-e1tv-ezs5-0uvb-5655 -> yt key transmition
//        $jitsiService = app(JitsiServiceContract::class);
//        dd($jitsiService);

        $authObject = app(AuthenticateServiceContract::class);
//        dd($authObject->getLoginUrl('hubert.krzysztofiak@escolasoft.com'));
        $code = '4/0AX4XfWhUBHltLpnHORNd-UUF30yO6rDhga5d2R-pBhmQXhgkxthN8wV87Cgs8Uc5g2EJcQ';
//        dd($authObject->getToken($code));
        $token = $authObject->refreshToken(config('youtube.refresh_token'));
////        dd($authObject->authChannelWithCode($token));
        $data = [
            "title" => "Test - " . now()->format('Y-m-d H:i:s'),
            "description" => "Test",			// Optional
            "event_start_date_time" => now()->format('Y-m-d H:i:s'),
            "event_end_date_time" => "",			// Optional
            "time_zone" => 'UTC',
            'privacy_status' => "public",				// default: "public" OR "private"
            "language_name" => "English",				// default: "English"
            "tag_array" => []				// Optional and should not be more than 500 characters
        ];
        $ytEventObj = app(LiveStreamServiceContract::class);
        $response = $ytEventObj->broadcast($token, $data);

        dd($response);
    }


    public function testServiceWithJwtAndSettings()
    {
        // public function getChannelData(User $user, string $channelDisplayName, bool $isModerator = false, array $configOverwrite = [], $interfaceConfigOverwrite = []): array

        $config = config("jitsi");
        $data = Jitsi::getChannelData($this->user, "Test Channel Name", true, ['foo' => 'bar'], ['bar' => 'foo']);

        $jwt = $this->decodeJWT($data['data']['jwt']);

        $this->assertEquals($data['data']['domain'], $config['host']);
        $this->assertEquals($data['data']['userInfo']['email'], $this->user->email);
        $this->assertEquals($jwt->user->email, $this->user->email);
        $this->assertEquals($jwt->user->moderator, true);
        $this->assertEquals($data['data']['configOverwrite'], ["foo" => "bar"]);
        $this->assertEquals($data['data']['interfaceConfigOverwrite'], ["bar" => "foo"]);
    }

    public function testDisabledServiceWithJwt()
    {
        // public function getChannelData(User $user, string $channelDisplayName, bool $isModerator = false, array $configOverwrite = [], $interfaceConfigOverwrite = []): array

        //$config = config("jitsi");

        config(['jitsi.package_status' => PackageStatusEnum::DISABLED]);


        $data = Jitsi::getChannelData($this->user, "Test Channel Name");
        $this->assertTrue(isset($data['error']));
    }
}
