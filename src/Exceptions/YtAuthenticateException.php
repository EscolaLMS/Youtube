<?php

namespace EscolaLms\Youtube\Exceptions;

use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class YtAuthenticateException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = $message ?: __('Youtube stream if not exists or is not authorize, log in.');
        $code = $code ?: 400;
        $youtubeServiceContract = app(YoutubeServiceContract::class);
        $youtubeServiceContract->dispatchYtError();
        parent::__construct($message, $code, $previous);
    }

    public function render($request): JsonResponse
    {
        return response()->json([
            'data' => [
                'code' => $this->getCode(),
                'message' => $this->getMessage()
            ]
        ], $this->getCode());
    }
}


