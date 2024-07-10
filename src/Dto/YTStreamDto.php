<?php

namespace EscolaLms\Youtube\Dto;

use EscolaLms\Youtube\Dto\Contracts\YTCdnDtoContract;
use EscolaLms\Youtube\Dto\Contracts\YTStreamDtoContract;
use Google\Service\YouTube\CdnSettings;
use Google\Service\YouTube\LiveStream;

class YTStreamDto implements YTStreamDtoContract
{
    private YTCdnDtoContract $YTCdnDto;
    private string $id;

    public function __construct(LiveStream $liveStream)
    {
        $this->id = $liveStream->getId();
        $this->setYTCdnDto($liveStream->getCdn());
    }

    public function getYTCdnDto(): YTCdnDtoContract
    {
        return $this->YTCdnDto;
    }

    private function setYTCdnDto(CdnSettings $cdnSettings)
    {
        $this->YTCdnDto = new YTCdnDto($cdnSettings);
    }
}
