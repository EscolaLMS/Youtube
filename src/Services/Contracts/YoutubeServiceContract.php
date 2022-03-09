<?php

namespace EscolaLms\Youtube\Services\Contracts;

use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Dto\YTLiveDto;

interface YoutubeServiceContract
{
    public function generateYTStream(YTBroadcastDto $YTBroadcastDto): ?YTLiveDto;
}
