<?php

namespace EscolaLms\Youtube\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Youtube\Http\Requests\GoogleGenerateUrlRequest;
use EscolaLms\Youtube\Services\Contracts\AuthServiceContract;
use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GoogleController extends EscolaLmsBaseController
{
    private YoutubeServiceContract $youtubeServiceContract;
    private AuthServiceContract $authServiceContract;

    public function __construct(
        AuthServiceContract $authServiceContract,
        YoutubeServiceContract $youtubeServiceContract
    ) {
        $this->authServiceContract = $authServiceContract;
        $this->youtubeServiceContract = $youtubeServiceContract;
    }

    public function generateUrl(GoogleGenerateUrlRequest $generateUrlRequest): Response
    {
        $url = $this->authServiceContract->getLoginUrl($generateUrlRequest->input('email'));
        return response([
            'url' => $url
        ]);
    }

    public function setRefreshToken(Request $request): void
    {
        $this->youtubeServiceContract->setRefreshToken($request->input('code'));
    }
}
