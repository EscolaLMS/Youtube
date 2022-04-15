<?php

namespace EscolaLms\Youtube\Services;

use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
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

    public function generateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract
    {
        $token = $this->authenticateServiceContract->refreshToken(config('services.youtube.refresh_token'));
        return $this->liveStreamServiceContract->broadcast($token, $YTBroadcastDto);
    }

    public function updateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract
    {
        $token = $this->authenticateServiceContract->refreshToken(config('services.youtube.refresh_token'));
        return $this->liveStreamServiceContract->updateBroadcast($token, $YTBroadcastDto);
    }

    public function removeYTStream(string $ytId): bool
    {
        $token = $this->authenticateServiceContract->refreshToken(config('services.youtube.refresh_token'));
        return $this->liveStreamServiceContract->deleteEvent($token, $ytId);
    }

    public function setRefreshToken(string $code): void
    {
        $token = $this->authenticateServiceContract->getToken($code);
        if (isset($token['refresh_token'])) {
            AdministrableConfig::setConfig([
                'services.youtube.refresh_token' => $token['refresh_token'],
            ]);
            AdministrableConfig::storeConfig();
        }
    }

    /**
     * @param YTBroadcastDto $YTBroadcastDto
     * @param string $broadcastStatus "testing" | "live" | "complete"
     * @return mixed
     */
    public function setStatusInLiveStream(YTBroadcastDto $YTBroadcastDto, string $broadcastStatus)
    {
        $token = $this->authenticateServiceContract->refreshToken(config('services.youtube.refresh_token'));
        return $this->liveStreamServiceContract->transitionEvent($token, $YTBroadcastDto, $broadcastStatus);
    }

}
