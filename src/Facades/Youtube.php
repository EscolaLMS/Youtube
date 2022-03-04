<?php

namespace EscolaLms\Youtube\Facades;

use EscolaLms\Youtube\Services\Contracts\YoutubeServiceContract;
use Illuminate\Support\Facades\Facade;

class Youtube extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return YoutubeServiceContract::class;
    }
}
