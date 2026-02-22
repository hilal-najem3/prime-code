<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use App\Models\Product;

class ProductCategory extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'parent_id', 'thumbnail_id', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_category', 'category_id', 'product_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}