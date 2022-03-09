<?php

namespace EscolaLms\Youtube\Dto;

use Google\Service\YouTube\Video;

class YTUpdateResponseDto
{
    private string $id;

    public function __construct(Video $video)
    {
        $this->id = $video->getId();
    }

    public function getId(): ?string
    {
        return $this->id ?? null;
    }
}
