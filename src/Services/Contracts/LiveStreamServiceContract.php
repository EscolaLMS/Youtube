<?php

namespace EscolaLms\Youtube\Services\Contracts;

use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Dto\YTLiveDto;

interface LiveStreamServiceContract
{
    public function broadcast($token, YTBroadcastDto $ytBroadcastDto): ?YTLiveDtoContract;
    public function updateBroadcast($token, YTBroadcastDto $YTBroadcastDto): ?YTLiveDto;
    public function deleteEvent($token, YTBroadcastDto $YTBroadcastDto): bool;
    public function transitionEvent($token, YTBroadcastDto $YTBroadcastDto, string $broadcastStatus);
}
