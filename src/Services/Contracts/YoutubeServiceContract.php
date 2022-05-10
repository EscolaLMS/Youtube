<?php

namespace EscolaLms\Youtube\Services\Contracts;

use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use Illuminate\Support\Collection;

interface YoutubeServiceContract
{
    public function generateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract;

    public function updateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract;

    public function removeYTStream(YTBroadcastDto $YTBroadcastDto): bool;

    public function setRefreshToken(string $code): void;

    public function setStatusInLiveStream(YTBroadcastDto $YTBroadcastDto, string $broadcastStatus);

    public function getYtLiveStream(YTBroadcastDto $YTBroadcastDto): Collection;

    public function generateYTAuthUrl(string $email): string;

    public function dispatchYtError(): void;
}
