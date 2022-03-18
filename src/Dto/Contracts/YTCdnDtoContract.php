<?php

namespace EscolaLms\Youtube\Dto\Contracts;

interface YTCdnDtoContract
{
    public function getStreamUrl(): ?string;
    public function getStreamName(): ?string;
}
