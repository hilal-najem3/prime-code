<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'code', 'symbol', 'default'];

    public static function getDefault()
    {
        return self::where('default', true)->first();
    }
}