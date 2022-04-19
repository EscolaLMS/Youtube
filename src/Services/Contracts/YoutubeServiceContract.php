<?php

namespace EscolaLms\Youtube\Services\Contracts;

use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;

interface YoutubeServiceContract
{
    public function generateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract;

    public function updateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDtoContract;

    public function removeYTStream(YTBroadcastDto $YTBroadcastDto): bool;
}
