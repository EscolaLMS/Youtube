<?php

namespace EscolaLms\Youtube\Dto\Contracts;

use EscolaLms\Youtube\Dto\YTStreamDto;

interface YTLiveDtoContract
{
    public function getYtUrl(): ?string;
    public function getId(): ?string;
    public function getYTStreamDto(): ?YTStreamDtoContract;
    public function getYtAutostartStatus(): ?bool;
}
