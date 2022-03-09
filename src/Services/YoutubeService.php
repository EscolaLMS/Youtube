<?php

namespace EscolaLms\Youtube\Services;

use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\Settings\Models\Config;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Dto\YTLiveDto;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;

class YoutubeService implements YoutubeServiceContract
{
    private AuthenticateServiceContract $authenticateServiceContract;
    private LiveStreamServiceContract $liveStreamServiceContract;

    public function __construct(
        AuthenticateServiceContract $authenticateServiceContract,
        LiveStreamServiceContract $liveStreamServiceContract
    ) {
        $this->authenticateServiceContract = $authenticateServiceContract;
        $this->liveStreamServiceContract = $liveStreamServiceContract;
    }

    public function generateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDto
    {
        $token = $this->authenticateServiceContract->refreshToken(config('youtube.refresh_token'));
        return $this->liveStreamServiceContract->broadcast($token, $YTBroadcastDto);
    }

    public function setRefreshToken($code)
    {
        $token = $this->authenticateServiceContract->getToken($code);
        AdministrableConfig::setConfig([
            'services.youtube.refresh_token' => $token['refresh_token'],
        ]);
        AdministrableConfig::storeConfig();
    }

}
