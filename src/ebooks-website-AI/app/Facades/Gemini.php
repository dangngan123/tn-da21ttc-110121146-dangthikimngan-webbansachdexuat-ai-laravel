<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Gemini extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\GeminiService::class;
    }
}

