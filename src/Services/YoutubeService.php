<?php

namespace EscolaLms\Youtube\Services;

use EscolaLms\Youtube\Exceptions\YtAuthenticateException;
use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Services\Contracts\AuthenticateServiceContract;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use Illuminate\Support\Collection;

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
        try {
            $token = $this->authenticateServiceContract->refreshToken($this->getRefreshToken());
        } catch (\Exception $exception) {
            throw new YtAuthenticateException();
        }
        return $this->liveStreamServiceContract->broadcast($token, $YTBroadcastDto);
    }

    public function updateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract
    {
        try {
            $token = $this->authenticateServiceContract->refreshToken($this->getRefreshToken());
        } catch (\Exception $exception) {
            throw new YtAuthenticateException();
        }
        return $this->liveStreamServiceContract->updateBroadcast($token, $YTBroadcastDto);
    }

    public function removeYTStream(YTBroadcastDto $YTBroadcastDto): bool
    {
        try {
            $token = $this->authenticateServiceContract->refreshToken($this->getRefreshToken());
        } catch (\Exception $exception) {
            throw new YtAuthenticateException();
        }
        return $this->liveStreamServiceContract->deleteEvent($token, $YTBroadcastDto);
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
        try {
            $token = $this->authenticateServiceContract->refreshToken($this->getRefreshToken());
        } catch (\Exception $exception) {
            throw new YtAuthenticateException();
        }
        return $this->liveStreamServiceContract->transitionEvent($token, $YTBroadcastDto, $broadcastStatus);
    }

    public function getYtLiveStream(YTBroadcastDto $YTBroadcastDto): Collection
    {
        try {
            $token = $this->authenticateServiceContract->refreshToken($this->getRefreshToken());
        } catch (\Exception $exception) {
            throw new YtAuthenticateException();
        }
        return $this->liveStreamServiceContract->getListLiveStream($token, $YTBroadcastDto);
    }

    private function getRefreshToken(): string
    {
        $refreshToken = config('services.youtube.refresh_token');
        if (!$refreshToken) {
            throw new YtAuthenticateException();
        }
        return $refreshToken;
    }

}
