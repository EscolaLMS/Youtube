<?php

namespace EscolaLms\Youtube\Services\Contracts;

use Google_Client;

interface AuthServiceContract
{
    public function refreshToken($token): array;
    public function getToken($code): array;
    public function getLoginUrl($youtube_email, $channelId = null): string;
    public function setAccessToken($google_token = null): bool;
    public function createResource($properties): array;
    public function addPropertyToResource(&$ref, $property, $value): void;
    public function parseTime($time): string;
    public function getClient(): Google_Client;
}
