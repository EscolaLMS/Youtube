<?php

namespace EscolaLms\Youtube\Dto;

use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\Contracts\YTStreamDtoContract;
use Google\Service\YouTube\LiveBroadcast;

class YTLiveDto implements YTLiveDtoContract
{
    private string $embedHtml;
    private string $ytUrl;
    private string $id;
    private ?bool $ytAutostartStatus;
    private YTStreamDtoContract $YTStreamDto;
    private YTUpdateResponseDto $YTUpdateResponseDto;

    public function __construct(LiveBroadcast $liveBroadcast)
    {
        $this->setEmbedHtml($liveBroadcast->getContentDetails()->getMonitorStream()->getEmbedHtml());
        $this->setYtUrl();
        $this->setId($liveBroadcast->getId());
    }

    public function getYtUrl(): ?string
    {
        return $this->ytUrl ?? '';
    }

    public function getEmbedHtml(): ?string
    {
        return $this->embedHtml ?? '';
    }

    public function getYTStreamDto(): ?YTStreamDtoContract
    {
        return $this->YTStreamDto ?? null;
    }

    public function getYTUpdateResponseDto(): ?YTUpdateResponseDto
    {
        return $this->YTUpdateResponseDto ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setYTStreamDto(YTStreamDtoContract $YTStreamDto): void
    {
        $this->YTStreamDto = $YTStreamDto;
    }

    public function setYTUpdateResponseDto(YTUpdateResponseDto $YTUpdateResponseDto): void
    {
        $this->YTUpdateResponseDto = $YTUpdateResponseDto;
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

    private function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getYtAutostartStatus(): ?bool
    {
        return $this->ytAutostartStatus ?? false;
    }

    public function setYtAutostartStatus(?bool $autostartStatus): void
    {
        $this->ytAutostartStatus = $autostartStatus;
    }
}
