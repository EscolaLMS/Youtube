<?php

namespace EscolaLms\Youtube\Dto;

use EscolaLms\Youtube\Dto\Traits\DtoHelper;

class YTBroadcastDto
{
    use DtoHelper;

    private string $title;
    private string $description;
    private string $thumbnailPath;
    private string $eventStartDateTime;
    private string $eventEndDateTime;
    private string $timeZone = 'UTC';
    private string $privacyStatus = 'unlisted';
    private string $languageName = 'Polish';
    private array $tagArray = [];

    public function __construct(array $data = [])
    {
        $this->setterByData($data);
    }

    public function getTitle(): ?string
    {
        return $this->title ?? null;
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

    public function setTagArray(array $tags): void
    {
        $this->tagArray = $tags;
    }
}
