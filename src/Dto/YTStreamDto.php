<?php

namespace EscolaLms\Youtube\Dto;

use Google\Service\YouTube\CdnSettings;
use Google\Service\YouTube\LiveStream;

class YTStreamDto
{
    private string $id;
    private YTCdnDto $YTCdnDto;

    public function __construct(LiveStream $liveStream)
    {
        $this->id = $liveStream->getId();
        $this->setYTCdnDto($liveStream->getCdn());
    }

    private function setYTCdnDto(CdnSettings $cdnSettings)
    {
        $this->YTCdnDto = new YTCdnDto($cdnSettings);
    }
}
