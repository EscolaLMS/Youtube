<?php

namespace EscolaLms\Youtube\Dto;

use EscolaLms\Youtube\Dto\Traits\DtoHelper;

class YTBroadcastDto
{
    use DtoHelper;

    private ?string $id;
    private string $title;
    private string $description;
    private string $thumbnailPath;
    private string $eventStartDateTime;
    private string $eventEndDateTime;
    private string $timeZone = 'UTC';
    private string $privacyStatus = 'unlisted';
    private string $languageName = 'Polish';
    private ?bool $autostartStatus = false;
    private array $tagArray = [];

    public function __construct(array $data = [])
    {
        $this->setterByData($data);
    }

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function getId(): ?string
    {
        return $this->id ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath ?? null;
    }

    public function getEventStartDateTime(): ?string
    {
        return $this->eventStartDateTime ?? null;
    }

    public function getTimeZone(): ?string
    {
        return $this->timeZone ?? null;
    }

    public function getPrivacyStatus(): ?string
    {
        return $this->privacyStatus ?? null;
    }

    public function getLanguageName(): ?string
    {
        return $this->languageName ?? null;
    }

    public function getEventEndDateTime(): ?string
    {
        return $this->eventEndDateTime ?? null;
    }

    public function getTagArray(): array
    {
        return $this->tagArray;
    }

    public function getAutostartStatus(): bool
    {
        return $this->autostartStatus ?? false;
    }

    public function setTagArray(array $tags): void
    {
        $this->tagArray = $tags;
    }

    public function setAutostartStatus(?bool $autostartStatus): void
    {
        $this->autostartStatus = $autostartStatus;
    }
}
