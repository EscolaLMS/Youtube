<?php

namespace EscolaLms\Youtube\Services;

use Config;
use EscolaLms\Youtube\Services\Contracts\AuthServiceContract;
use Exception;
use Google_Client;

/**
 *  Api Service For Auth
 */
class AuthService implements AuthServiceContract
{
	protected Google_Client $client;
	protected ?string $ytLanguage;

	public function __construct()
    {
		$this->client = new Google_Client;

		$this->client->setClientId(Config::get('services.youtube.client_id'));
		$this->client->setClientSecret(Config::get('services.youtube.client_secret'));
		$this->client->setDeveloperKey(Config::get('services.youtube.api_key'));
		$this->client->setRedirectUri(Config::get('services.youtube.redirect_url'));

		$this->client->setScopes([
			'https://www.googleapis.com/auth/youtube',
		]);

		$this->client->setAccessType('offline');
		$this->client->setPrompt('consent');
		$this->ytLanguage = Config::get('google.yt_language');

	}

    public function refreshToken($token): array
    {
        return $this->client->refreshToken($token);
    }

	/**
	 * [getToken -generate token from response code recived on visiting the login url generated]
	 * @param  string $code [code for auth]
	 */
	public function getToken($code): array
    {
        $this->client->fetchAccessTokenWithAuthCode($code);
        return $this->client->getAccessToken();
	}

	/**
	 * [getLoginUrl - generates the url login url to generate auth token]
	 * @param  string $youtube_email [account to be authenticated]
	 * @param  string $channelId     [return
	 * @return string              [auth url to generate]
	 */
	public function getLoginUrl($youtube_email, $channelId = null): string
    {
        if (!empty($channelId)) {
            $this->client->setState($channelId);
        }

        $this->client->setLoginHint($youtube_email);
        return $this->client->createAuthUrl();
	}

	/**
	 * [setAccessToken -setting the access token to the client]
	 * @param string $google_token [google auth token]
	 */
	public function setAccessToken($google_token = null): bool
    {
        if (!is_null($google_token)) {
            $this->client->setAccessToken($google_token);
        }

        if (!is_null($google_token) && $this->client->isAccessTokenExpired()) {
            $refreshed_token = $this->client->getRefreshToken();
            $this->client->fetchAccessTokenWithRefreshToken($refreshed_token);
            $newToken = $this->client->getAccessToken();
            $newToken = json_encode($newToken);
        }
        return !$this->client->isAccessTokenExpired();
	}

	/**
	 * [createResource creating a resource array and addind properties to it]
	 * @param  $properties [param properties to be added to channel]
	 */
	public function createResource($properties): array
    {
        $resource = [];
        foreach ($properties as $prop => $value) {

            if ($value) {
                /**
                 * add property to resource
                 */
                $this->addPropertyToResource($resource, $prop, $value);
            }
        }

        return $resource;
	}

	/**
	 * [addPropertyToResource description]
	 * @param &$ref     [using reference of array from createResource to add property to it]
	 * @param $property [property to be inserted to resource array]
	 */
	public function addPropertyToResource(&$ref, $property, $value): void
    {
        $keys = explode(".", $property);
        $isArray = false;
        foreach ($keys as $key) {
            /**
             * snippet.tags[]  [convert to snippet.tags]
             * a boolean variable  [to handle the value like an array]
             */
            if (substr($key, -2) === "[]") {
                $key = substr($key, 0, -2);
                $isArray = true;
            }
            $ref = &$ref[$key];
        }

        /**
         * Set the property value [ handling the array values]
         */
        if ($isArray && $value) {
            $ref = explode(",", $value);
        } elseif ($isArray) {
            $ref = [];
        } else {
            $ref = $value;
        }
	}

	/**
	 * [parseTime - parse the video time in to description format]
	 * @param  $time [youtube returned time format]
	 */
	public function parseTime($time): string
    {
		$tempTime = str_replace("PT", " ", $time);
		$tempTime = str_replace('H', " Hours ", $tempTime);
		$tempTime = str_replace('M', " Minutes ", $tempTime);
		$tempTime = str_replace('S', " Seconds ", $tempTime);

		return $tempTime;
	}

    public function getClient(): Google_Client
    {
        return $this->client;
    }

}
