<?php

namespace App\Helpers\Joom;
 
class Links
{
    public static function add_button($route, $url_extra = '') 
    {
        return '<a class="btn btn-primary btn-sm mb-4" href="'.route('sitemap').$url_extra.'">Add new</a>';
    }

    public static function return_button($route, $url_extra = '') 
    {
        return '<a class="btn btn-secondary btn-sm mb-2" href="'.route('sitemap').$url_extra.'">Return</a>';
    }
}
?>