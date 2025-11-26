<?php

namespace App\Lib\Helper;

use App\Models\Option;
use Illuminate\Support\Facades\Crypt;

class TokenCassa{
    public static function set()
    {
        $encrypted = Option::where('option_key', 'token_cassa')->value('option_value');
        $token = Crypt::decryptString($encrypted);

        return $token;
    }
}
