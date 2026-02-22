<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Media;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\AttributeValue;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'type',
        'sku',
        'name',
        'short_description',
        'description',
        'details',
        'price',
        'sale_price',
        'currency_id',
        'quantity',
        'unit',
        'thumbnail_id',
        'is_active',
        'is_featured',
        'sort_order',
        'meta',
    ];

    public $translatable = [
        'name',
        'meta',
        'short_description',
        'description',
        'details',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function toFrontend()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'sku' => $this->sku,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'currency_id' => $this->currency_id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail->file_path) : null,
            'children' => $this->children
                ? $this->children->map(fn($child) => [
                    'id' => $child->id,
                    'sku' => $child->sku,
                    'price' => $child->price,
                    'sale_price' => $child->sale_price,
                    'currency_id' => $child->currency_id,
                    'quantity' => $child->quantity,
                    'unit' => $child->unit,
                    'is_active' => $child->is_active,
                    'thumbnail' => $child->thumbnail ? asset('storage/' . $child->thumbnail->file_path) : null,
                    'attribute_values' => $child->attributeValues->pluck('id'),
                ])->values()->all()
                : [],
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category', 'product_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag', 'product_id', 'tag_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}