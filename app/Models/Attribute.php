<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'type',
        'is_required',
        'is_filterable',
        'sort_order',
    ];

    public $translatable = [
        'name',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
