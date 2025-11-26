<?php

namespace App\Lib\Helper;

use App\Models\Option;

class UrlApi{
    public static function url(){
        return Option::getOption("endpoint_meteora");
    }
}
