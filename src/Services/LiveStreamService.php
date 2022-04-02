<?php

namespace EscolaLms\Youtube\Services;

use Carbon\Carbon;
use EscolaLms\Youtube\Dto\Contracts\YTLiveDtoContract;
use EscolaLms\Youtube\Dto\YTBroadcastDto;
use EscolaLms\Youtube\Dto\YTLiveDto;
use EscolaLms\Youtube\Dto\YTStreamDto;
use EscolaLms\Youtube\Dto\YTUpdateResponseDto;
use EscolaLms\Youtube\Services\Contracts\LiveStreamServiceContract;
use Google\Service\YouTube\VideoSnippet;
use Google_Service_YouTube_CdnSettings;
use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;
use Google_Service_YouTube_LiveStream;
use Google_Service_YouTube_LiveStreamSnippet;
use Google_Service_YouTube_VideoRecordingDetails;

/**
 *  Api Service For Youtube Live Events
 */
class LiveStreamService extends AuthService implements LiveStreamServiceContract
{
	protected $youtube;
	protected Google_Service_YouTube_LiveBroadcastSnippet $googleLiveBroadcastSnippet;
	protected Google_Service_YouTube_LiveBroadcastStatus $googleLiveBroadcastStatus;
	protected Google_Service_YouTube_LiveBroadcast $googleYoutubeLiveBroadcast;
	protected Google_Service_YouTube_LiveStreamSnippet $googleYoutubeLiveStreamSnippet;
	protected Google_Service_YouTube_CdnSettings $googleYoutubeCdnSettings;
	protected Google_Service_YouTube_LiveStream $googleYoutubeLiveStream;
	protected Google_Service_YouTube_VideoRecordingDetails $googleYoutubeVideoRecordingDetails;

	public function __construct() {
		parent::__construct();
		$this->googleLiveBroadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet;
		$this->googleLiveBroadcastStatus = new Google_Service_YouTube_LiveBroadcastStatus;
		$this->googleYoutubeLiveBroadcast = new Google_Service_YouTube_LiveBroadcast;
		$this->googleYoutubeLiveStreamSnippet = new Google_Service_YouTube_LiveStreamSnippet;
		$this->googleYoutubeCdnSettings = new Google_Service_YouTube_CdnSettings;
		$this->googleYoutubeLiveStream = new Google_Service_YouTube_LiveStream;
		$this->googleYoutubeVideoRecordingDetails = new Google_Service_YouTube_VideoRecordingDetails;

	}

	/**
	 * [broadcast creating the event on youtube]
	 * @param  [type] $token [auth token for youtube channel]
	 * @param  [type] $data  [array of the event details]
	 * @return [type]        [response array of broadcast ]
	 */
	public function broadcast($token, YTBroadcastDto $ytBroadcastDto): ?YTLiveDtoContract
    {
        if (!$ytBroadcastDto->getTitle() || !$ytBroadcastDto->getDescription()) {
            return null;
        }
        /**
         * [setAccessToken [setting accent token to client]]
         */
        $setAccessToken = $this->setAccessToken($token);
        if (!$setAccessToken) {
            return null;
        }
        /**
         * [$service [instance of Google_Service_YouTube ]]
         * @var [type]
         */
        $youtube = new \Google_Service_YouTube($this->client);
        $startdt = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $ytBroadcastDto->getEventStartDateTime(),
            $ytBroadcastDto->getTimeZone()
        );
        $now = Carbon::now($ytBroadcastDto->getTimeZone());
        $startdt = ($startdt < $now) ? $now : $startdt;
        $startdtIso = $startdt->toIso8601String();
        if ($ytBroadcastDto->getEventEndDateTime()) {
            $enddt = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $ytBroadcastDto->getEventEndDateTime(),
                $ytBroadcastDto->getTimeZone()
            );
            $enddt = ($enddt < $now) ? $now : $enddt;
            $enddtIso = $enddt->toIso8601String();
        }
        if (count($ytBroadcastDto->getTagArray()) > 0) {
            $tags = substr(
                str_replace(
                    ", ,",
                    ",", implode(',', $ytBroadcastDto->getTagArray())
                ),
                0,
                498);
            $tags = (substr($tags, -1) === ',') ? substr($tags, 0, -1) : $tags;
            $ytBroadcastDto->setTagArray(explode(',', $tags));
        }
        $language = $ytBroadcastDto->getLanguageName();
        /**
         * Create an object for the liveBroadcast resource [specify snippet's title, scheduled start time, and scheduled end time]
         */
        $this->googleLiveBroadcastSnippet->setTitle($ytBroadcastDto->getTitle());
        $this->googleLiveBroadcastSnippet->setDescription($ytBroadcastDto->getDescription());
        $this->googleLiveBroadcastSnippet->setScheduledStartTime($startdtIso);
        if ($ytBroadcastDto->getEventEndDateTime()) {
            $this->googleLiveBroadcastSnippet->setScheduledEndTime($enddtIso);
        }
        /**
         * object for the liveBroadcast resource's status ["private, public or unlisted"]
         */
        $this->googleLiveBroadcastStatus->setPrivacyStatus($ytBroadcastDto->getPrivacyStatus());
        /**
         * API Request [inserts the liveBroadcast resource]
         */
        $this->googleYoutubeLiveBroadcast->setSnippet($this->googleLiveBroadcastSnippet);
        $this->googleYoutubeLiveBroadcast->setStatus($this->googleLiveBroadcastStatus);
        $this->googleYoutubeLiveBroadcast->setKind('youtube#liveBroadcast');
        /**
         * Execute Insert LiveBroadcast Resource Api [return an object that contains information about the new broadcast]
         */
        $broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status', $this->googleYoutubeLiveBroadcast, []);
        $youtubeEventId = $broadcastsResponse['id'];
        /**
         * set thumbnail to the event
         */
        if (!is_null($ytBroadcastDto->getThumbnailPath())) {
            $thumb = $this->uploadThumbnail($ytBroadcastDto->getThumbnailPath(), $youtubeEventId);
        }

        /**
         * Call the API's videos.list method to retrieve the video resource.
         */
        $listResponse = $youtube->videos->listVideos("snippet", array('id' => $youtubeEventId));
        $video = $listResponse[0];
        /**
         * update the tags and language via video resource
         */
        $videoSnippet = $video['snippet'];
        /* @var $videoSnippet VideoSnippet */
        $videoSnippet->setTags($ytBroadcastDto->getTagArray());
        if (!is_null($language)) {
            $temp = isset($this->ytLanguage[$language]) ? $this->ytLanguage[$language] : "en";
            $videoSnippet->setDefaultAudioLanguage($temp);
            $videoSnippet->setDefaultLanguage($temp);
        }
        $video['snippet'] = $videoSnippet;
        /**
         * Update video resource [videos.update() method.]
         */
        $updateResponse = $youtube->videos->update("snippet", $video);
        $ytUpdateResponseDto = new YTUpdateResponseDto($updateResponse);
        /**
         * object of livestream resource [snippet][title]
         */
        $this->googleYoutubeLiveStreamSnippet->setTitle($ytBroadcastDto->getTitle());
        /**
         * object for content distribution  [stream's format,ingestion type.]
         */
        $this->googleYoutubeCdnSettings->setResolution("variable");
        $this->googleYoutubeCdnSettings->setFrameRate("variable");
        $this->googleYoutubeCdnSettings->setIngestionType('rtmp');
        /**
         * API request [inserts liveStream resource.]
         */
        $this->googleYoutubeLiveStream->setSnippet($this->googleYoutubeLiveStreamSnippet);
        $this->googleYoutubeLiveStream->setCdn($this->googleYoutubeCdnSettings);
        $this->googleYoutubeLiveStream->setKind('youtube#liveStream');
        /**
         * execute the insert request [return an object that contains information about new stream]
         */
        $streamsResponse = $youtube->liveStreams->insert('snippet,cdn', $this->googleYoutubeLiveStream, []);
        $ytStreamDto = new YTStreamDto($streamsResponse);
        /**
         * Bind the broadcast to the live stream
         */
        $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
            $broadcastsResponse['id'], 'id,contentDetails',
            [
                'streamId' => $streamsResponse['id'],
            ]);
        $ytLiveDto = new YTLiveDto($bindBroadcastResponse);
        $ytLiveDto->setYTStreamDto($ytStreamDto);
        $ytLiveDto->setYTUpdateResponseDto($ytUpdateResponseDto);
        return $ytLiveDto;
	}

    /**
     * [uploadThumbnail upload thumbnail for the event]
     * @param  string $url     [path to image]
     * @param  [type] $videoId [eventId]
     * @return [type]          [thumbnail url]
     */
	public function uploadThumbnail(string $url, $videoId)
    {
		if ($this->client->getAccessToken()) {
            /**
             * [$service [instance of Google_Service_YouTube ]]
             * @var [type]
             */
            $youtube = new \Google_Service_YouTube($this->client);
            $imagePath = $url;
            /**
             * size of chunk to be uploaded  in bytes [default  1 * 1024 * 1024] (Set a higher value for reliable connection as fewer chunks lead to faster uploads)
             */
            $chunkSizeBytes = 1 * 1024 * 1024;
            $this->client->setDefer(true);
            /**
             * Setting the defer flag to true tells the client to return a request which can be called with ->execute(); instead of making the API call immediately
             */
            $setRequest = $youtube->thumbnails->set($videoId);
            /**
             * MediaFileUpload object [resumable uploads]
             */
            $media = new \Google_Http_MediaFileUpload(
                $this->client,
                $setRequest,
                'image/png',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($imagePath));
            /**
             * Read the media file [to upload chunk by chunk]
             */
            $status = false;
            $handle = fopen($imagePath, "rb");
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }
            fclose($handle);
            /**
             * set defer to false [to make other calls after the file upload]
             */
            $this->client->setDefer(false);
            $thumbnailUrl = $status['items'][0]['default']['url'];
            return $thumbnailUrl;
		}
	}

	/**
	 * [updateTags description]
	 * @param  [type] $videoId   [eventID]
	 * @param  array  $tagsArray [array of tags]
	 */
	public function updateTags($videoId, $tagsArray = []): void
    {
		if ($this->client->getAccessToken()) {
            /**
             * [$service [instance of Google_Service_YouTube ]]
             * @var [type]
             */
            $youtube = new \Google_Service_YouTube($this->client);
            $videoId = $videoId;
            /**
             * [$listResponse videos.list method to retrieve the video resource.]
             */
            $listResponse = $youtube->videos->listVideos("snippet",
                array('id' => $videoId));
            $video = $listResponse[0];
            $videoSnippet = $video['snippet'];
            $videoSnippet['tags'] = $tagsArray["tag_array"];
            $video['snippet'] = $videoSnippet;
            /**
             * [$updateResponse calling the videos.update() method.]
             */
            $updateResponse = $youtube->videos->update("snippet", $video);
		}
	}

    /**
     * [transitionEvent transition the state of event [test, start streaming , stop streaming]]
     * @param  [type] $token            [auth token for the channel]
     * @param  [type] $youtubeEventId [eventId]
     * @param  [type] $broadcastStatus  [transition state - ["testing", "live", "complete"]]
     * @return [type]                   [transition status]
     */
	public function transitionEvent($token, $youtubeEventId, $broadcastStatus)
    {
        if (!empty($token)) {
            return false;
        }
        /**
         * [setAccessToken [setting accent token to client]]
         */
        $setAccessToken = $this->setAccessToken($token);
        if (!$setAccessToken) {
            return false;
        }

        $part = "status, id, snippet";
        /**
         * [$service [instance of Google_Service_YouTube ]]
         * @var [type]
         */
        $youtube = new \Google_Service_YouTube($this->client);
        $liveBroadcasts = $youtube->liveBroadcasts;
        $transition = $liveBroadcasts->transition($broadcastStatus, $youtubeEventId, $part);
        return $transition;
	}

	/**
	 * [updateBroadcast update the already created event on youtunbe channel]
	 * @param  [type] $token            [channel auth token]
	 * @param  [type] $data             [event details]
	 * @param  [type] $youtubeEventId [eventID]
	 * @return [type]                   [response array for various process in the update]
	 */
	public function updateBroadcast($token, YTBroadcastDto $YTBroadcastDto): ?YTLiveDto
    {
        /**
         * [setAccessToken [setting accent token to client]]
         */
        $setAccessToken = $this->setAccessToken($token);
        if (!$setAccessToken) {
            return null;
        }
        /**
         * [$service [instance of Google_Service_YouTube ]]
         * @var [type]
         */
        $youtube = new \Google_Service_YouTube($this->client);
        /**
         *  parsing event start date
         */
        $startdt = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $YTBroadcastDto->getEventStartDateTime(),
            $YTBroadcastDto->getTimeZone()
        );
        $now = Carbon::now($YTBroadcastDto->getTimeZone());
        $startdt = ($startdt < $now) ? $now : $startdt;
        $startdtIso = $startdt->toIso8601String();
        /**
         * parsing event end date
         */
        if ($YTBroadcastDto->getEventStartDateTime()) {
            $enddt = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $YTBroadcastDto->getEventStartDateTime(),
                $YTBroadcastDto->getTimeZone()
            );
            $enddt = ($enddt < $now) ? $now : $enddt;
            $enddtIso = $enddt->toIso8601String();
        }
        $tags = substr(str_replace(
            ", ,",
            ",",
            implode(',', $YTBroadcastDto->getTagArray())
        ), 0, 498);
        $tags = (substr($tags, -1) === ',') ? substr($tags, 0, -1) : $tags;
        $YTBroadcastDto->setTagArray(explode(',', $tags));
        $language = $YTBroadcastDto->getLanguageName();
        /**
         * Create an object for the liveBroadcast resource's snippet [snippet's title, scheduled start time, and scheduled end time.]
         */
        $this->googleLiveBroadcastSnippet->setTitle($YTBroadcastDto->getTitle());
        $this->googleLiveBroadcastSnippet->setDescription($YTBroadcastDto->getDescription());
        $this->googleLiveBroadcastSnippet->setScheduledStartTime($startdtIso);
        if ($YTBroadcastDto->getEventStartDateTime()) {
            $this->googleLiveBroadcastSnippet->setScheduledEndTime($enddtIso);
        }
        /**
         * Create an object for the liveBroadcast resource's status ["private, public or unlisted".]
         */
        $this->googleLiveBroadcastStatus->setPrivacyStatus($YTBroadcastDto->getPrivacyStatus());
        /**
         * Create the API request  [inserts the liveBroadcast resource.]
         */
        $this->googleYoutubeLiveBroadcast->setSnippet($this->googleLiveBroadcastSnippet);
        $this->googleYoutubeLiveBroadcast->setStatus($this->googleLiveBroadcastStatus);
        $this->googleYoutubeLiveBroadcast->setKind('youtube#liveBroadcast');
        $this->googleYoutubeLiveBroadcast->setId($YTBroadcastDto->getId());
        /**
         * Execute the request [return info about the new broadcast ]
         */
        $youtube->liveBroadcasts->update('snippet,status', $this->googleYoutubeLiveBroadcast, []);
//			/**
//			 * set thumbnail
//			 */
//			if (!is_null($YTBroadcastDto->getThumbnailPath())) {
//				$thumb = $this->uploadThumbnail($YTBroadcastDto->getThumbnailPath(), $youtubeEventId);
//			}
        /**
         * Call the API's videos.list method [retrieve the video resource]
         */
        $listResponse = $youtube->videos->listVideos("snippet", ['id' => $YTBroadcastDto->getId()]);
        $video = $listResponse[0];
        $videoSnippet = $video['snippet'];
        /* @var $videoSnippet VideoSnippet */
        $videoSnippet->setTags($YTBroadcastDto->getTagArray());
        /**
         * set Language and other details
         */
        if (!is_null($language)) {
            $temp = $this->ytLanguage[$language] ?? "en";
            $videoSnippet->setDefaultAudioLanguage($temp);
            $videoSnippet->setDefaultLanguage($temp);
        }
        $videoSnippet->setTitle($YTBroadcastDto->getTitle());
        /*
         * Category education from YT
         {
            "kind": "youtube#videoCategory",
            "etag": "yBaNkLx4sX9NcDmFgAmxQcV4Y30",
            "id": "27",
            "snippet": {
              "title": "Education",
              "assignable": true,
              "channelId": "UCBR8-60-B28hp2BmDPdntcQ"
            }
         }
        */
        $videoSnippet->setCategoryId(27);
        $videoSnippet->setDescription($YTBroadcastDto->getDescription());
        $videoSnippet->setPublishedAt($startdtIso);
        $video['snippet'] = $videoSnippet;
        /**
         * Update the video resource  [call videos.update() method]
         */
        $updateResponse = $youtube->videos->update("snippet", $video);
        $YTUpdateResponseDto = new YTUpdateResponseDto($updateResponse);
        $this->googleYoutubeLiveStreamSnippet->setTitle($YTBroadcastDto->getTitle());
        /**
         * object for content distribution  [stream's format,ingestion type.]
         */
        $this->googleYoutubeCdnSettings->setResolution("variable");
        $this->googleYoutubeCdnSettings->setFrameRate("variable");
        $this->googleYoutubeCdnSettings->setIngestionType('rtmp');
        /**
         * API request [inserts liveStream resource.]
         */
        $this->googleYoutubeLiveStream->setSnippet($this->googleYoutubeLiveStreamSnippet);
        $this->googleYoutubeLiveStream->setCdn($this->googleYoutubeCdnSettings);
        $this->googleYoutubeLiveStream->setKind('youtube#liveStream');
        /**
         * execute the insert request [return an object that contains information about new stream]
         */
        $streamsResponse = $youtube->liveStreams->insert('snippet,cdn', $this->googleYoutubeLiveStream, array());
        $ytStreamDto = new YTStreamDto($streamsResponse);
        /**
         * Bind the broadcast to the live stream
         */
        $bindBroadcastResponse = $youtube->liveBroadcasts->bind(
            $updateResponse['id'], 'id,contentDetails',
            [
                'streamId' => $streamsResponse['id'],
            ]);
        $YTLiveDto = new YTLiveDto($bindBroadcastResponse);
        $YTLiveDto->setYTStreamDto($ytStreamDto);
        $YTLiveDto->setYTUpdateResponseDto($YTUpdateResponseDto);

        return $YTLiveDto;
	}

	/**
	 * [deleteEvent delete an event created in youtube]
	 * @param  [type] $token            [auth token for channel]
	 * @param  [type] $youtubeEventId [eventID]
	 * @return [type]                   [deleteBroadcastsResponse]
	 */
	public function deleteEvent($token, $youtubeEventId): bool
    {
        /**
         * [setAccessToken [setting accent token to client]]
         */
        $setAccessToken = $this->setAccessToken($token);
        if (!$setAccessToken) {
            return false;
        }
        /**
         * [$service [instance of Google_Service_YouTube ]]
         * @var [type]
         */
        $youtube = new \Google_Service_YouTube($this->client);
        $deleteBroadcastsResponse = $youtube->liveBroadcasts->delete($youtubeEventId);
        return strpos($deleteBroadcastsResponse->getStatusCode(), '20') !== false;
	}
}
