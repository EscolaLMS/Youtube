<?php

namespace EscolaLms\Youtube\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Youtube\Http\Requests\GoogleGenerateUrlRequest;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GoogleController extends EscolaLmsBaseController
{
    private YoutubeServiceContract $youtubeServiceContract;

    public function __construct(
        YoutubeServiceContract $youtubeServiceContract
    ) {
        $this->youtubeServiceContract = $youtubeServiceContract;
    }

    public function generateUrl(GoogleGenerateUrlRequest $generateUrlRequest): Response
    {
        return response([
            'url' => $this->youtubeServiceContract->generateYTAuthUrl($generateUrlRequest->input('email'))
        ]);
    }

    public function setRefreshToken(Request $request): void
    {
        $this->youtubeServiceContract->setRefreshToken($request->input('code'));
    }
}
