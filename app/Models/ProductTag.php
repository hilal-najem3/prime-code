<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use Spatie\Translatable\HasTranslations;

class ProductTag extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_tag', 'tag_id', 'product_id');
    }
}