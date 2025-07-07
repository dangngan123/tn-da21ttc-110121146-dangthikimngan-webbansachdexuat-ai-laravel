<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function setLocale($lang)
    {
        // Validate the locale/ Add other supported locales here
        if (in_array($lang, ['vi', 'en'])) {
            App::setLocale($lang);
            Session::put('locale', $lang);
        }
        // Redirect back to the previous page
        return back();
    }
}
