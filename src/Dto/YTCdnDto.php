<?php

namespace EscolaLms\Youtube\Dto;

use Google\Service\YouTube\CdnSettings;

class YTCdnDto
{
    private string $streamUrl;
    private string $streamName;

    public function __construct(CdnSettings $cdnSettings)
    {
        $this->streamUrl = $cdnSettings->getIngestionInfo()->getIngestionAddress();
        $this->streamName = $cdnSettings->getIngestionInfo()->getStreamName();
    }

    public function getStreamUrl(): ?string
    {
        return $this->streamUrl ?? '';
    }

    public function getStreamName(): ?string
    {
        return $this->streamName ?? '';
    }
}
