<?php

namespace EscolaLms\Youtube\Dto;

use Google\Service\YouTube\LiveBroadcast;

class YTLiveDto
{
    private string $embedHtml;
    private string $ytUrl;
    private YTStreamDto $YTStreamDto;

    public function __construct(LiveBroadcast $liveBroadcast)
    {
        $this->setEmbedHtml($liveBroadcast->getContentDetails()->getMonitorStream()->getEmbedHtml());
        $this->setYtUrl();
    }

    public function getYtUrl(): ?string
    {
        return $this->ytUrl ?? '';
    }

    public function getEmbedHtml(): ?string
    {
        return $this->embedHtml ?? '';
    }

    public function getYTStreamDto(): ?YTStreamDto
    {
        return $this->YTStreamDto ?? null;
    }

    public function setYTStreamDto(YTStreamDto $YTStreamDto): void
    {
        $this->YTStreamDto = $YTStreamDto;
    }

    private function setYtUrl(): void
    {
        preg_match('/src *= *["\']?(?<source>[^"\']*)/', $this->embedHtml, $output);
        $this->ytUrl = $output['source'] ?? '';
    }

    private function setEmbedHtml(string $embedHtml): void
    {
        $this->embedHtml = $embedHtml;
    }
}
