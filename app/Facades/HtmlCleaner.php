<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string clean(string $html)
 * */
class HtmlCleaner extends Facade {
    protected static function getFacadeAccessor() {
        return 'html_cleaner';
    }
}
