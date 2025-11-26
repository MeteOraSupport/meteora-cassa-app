<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = "options";

    protected $fillable = [
        'option_key',
        'option_value'
    ];

    public static function getOption($option_key)
    {
        return Option::where('option_key', $option_key)->value('option_value');
    }
}
