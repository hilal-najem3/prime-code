<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;
use App\Models\Product;

class AttributeValue extends Model
{
    use HasTranslations;

    protected $fillable = [
        'attribute_id',
        'value',
        'color',
    ];

    public $translatable = [
        'value',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values');
    }
}
