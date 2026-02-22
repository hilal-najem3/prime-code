<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Products\CreateProductRequest;
use App\Http\Requests\Products\Products\UpdateProductRequest;
use App\Models\{
    Product,
    Media,
    Language,
    ProductCategory,
    ProductTag,
    Attribute,
    Currency
};
use Illuminate\Support\Facades\{
    DB,
    Log,
    Storage
};
use Exception;

class ProductsController extends Controller
{
    /*----------------------------------------
     | INDEX
     ----------------------------------------*/
    public function index()
    {
        $products = Product::query()
            ->where(function ($q) {
                $q->where('type', 'simple')
                    ->whereNull('parent_id');
            })
            ->orWhere('type', 'classified')
            ->get();
        return view('admin.products.products.index', compact('products'));
    }

    /*----------------------------------------
     | CREATE
     ----------------------------------------*/
    public function create()
    {
        $languages = Language::active()->get();
        $categories = ProductCategory::all();
        $tags = ProductTag::all();
        $currencies = Currency::all();
        $attributes = Attribute::with('values')->get();

        $attributesMap = $attributes->mapWithKeys(fn($attr) => [
            mb_strtolower(trim($attr->getTranslation('name', app()->getLocale())
                ?? (is_array($attr->name) ? reset($attr->name) : $attr->name)), 'UTF-8')
            => $attr->values->mapWithKeys(fn($val) => [
                $val->id => trim($val->getTranslation('value', app()->getLocale())
                    ?? (is_array($val->value) ? reset($val->value) : $val->value))
            ])->toArray()
        ])->toArray();

        return view('admin.products.products.create', compact(
            'languages',
            'categories',
            'tags',
            'currencies',
            'attributes',
            'attributesMap'
        ));
    }

    /*----------------------------------------
     | STORE
     ----------------------------------------*/
    public function store(CreateProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $product = $this->saveProduct(new Product(), $request->validated(), $request);

            // Save children if classified
            if ($product->type === 'classified') {
                $this->saveChildren($product, $request->input('children', []));
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Creation Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while creating the product.']);
        }
    }

    /*----------------------------------------
     | EDIT
     ----------------------------------------*/
    public function edit(string $id)
    {
        $languages = Language::active()->get();
        $categories = ProductCategory::all();
        $tags = ProductTag::all();
        $attributes = Attribute::with('values')->get();
        $currencies = Currency::all();

        $attributesMap = $attributes->mapWithKeys(fn($attr) => [
            mb_strtolower(trim($attr->getTranslation('name', app()->getLocale())
                ?? (is_array($attr->name) ? reset($attr->name) : $attr->name)), 'UTF-8')
            => $attr->values->mapWithKeys(fn($val) => [
                $val->id => trim($val->getTranslation('value', app()->getLocale())
                    ?? (is_array($val->value) ? reset($val->value) : $val->value))
            ])->toArray()
        ])->toArray();


        $product = Product::with([
            'children',
            'children.thumbnail',
            'children.attributeValues.attribute',
            'categories',
            'tags',
            'thumbnail'
        ])->findOrFail($id);

        $product->media = $product->media()->where('id', '!=', optional($product->thumbnail)->id)->get();

        $product->children->transform(function ($child) {
            $attribute_values = [];

            $child->attribute_values = $attribute_values;
            // Convert attribute values into key/value pairs like ['size' => 'XS']
            foreach ($child->attributeValues as $attrVal) {
                $attrName = strtolower($attrVal->attribute->getTranslation('name', app()->getLocale()));
                $attrValue = $attrVal->getTranslation('value', app()->getLocale());
                $child[$attrName] = $attrValue;

                $attribute_values[] = [
                    'attribute_id' => $attrVal->attribute_id,
                    'value_id' => $attrVal->id,
                ];
            }

            $child->attribute_values = $attribute_values;

            // Add thumbnail file_path (if exists)
            $child->thumbnail_url = $child->thumbnail ? Storage::url($child->thumbnail->file_path) : null;

            // You can also drop unneeded relationships if desired
            unset($child->attributeValues, $child->thumbnail);

            return $child;
        });

        $product->thumbnail_url = $product->thumbnail
            ? Storage::url($product->thumbnail->file_path)
            : null;

        // dd(
        //     $product->toArray(),
        //     $product->children->toArray(),
        //     $product->thumbnail->toArray(),
        //     $languages->toArray(),
        //     $attributesMap
        // );

        return view('admin.products.products.edit', compact(
            'product',
            'languages',
            'categories',
            'tags',
            'currencies',
            'attributes',
            'attributesMap'
        ));
    }

    /*----------------------------------------
     | UPDATE
     ----------------------------------------*/
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $this->saveProduct($product, $validated, $request);

            // Handle children update/create/delete
            $this->updateChildren($product, $validated['children'] ?? []);

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating the product.']);
        }
    }

    /*----------------------------------------
     | DESTROY
     ----------------------------------------*/
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::with(['media', 'tags', 'categories'])->findOrFail($id);

            foreach ($product->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }

            Storage::disk('public')->deleteDirectory("products/{$product->id}");
            $product->categories()->detach();
            $product->tags()->detach();
            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product Deletion Error: ' . $e->getMessage());
            return redirect()->route('admin.products.index')->with('error', 'Error deleting product.');
        }
    }

    /*----------------------------------------
     | PRIVATE HELPERS
     ----------------------------------------*/

    private function saveProduct(Product $product, array $data, $request): Product
    {
        $product->fill([
            'type' => $data['type'] ?? $product->type ?? 'simple',
            'sku' => $data['sku'] ?? null,
            'price' => $data['price'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'currency_id' => $data['currency_id'] ?? null,
            'quantity' => $data['quantity'] ?? 0,
            'unit' => $data['unit'] ?? '',
            'is_active' => $data['is_active'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
            'meta' => $data['meta'] ?? [],
        ]);

        // translations
        if (isset($data['name'])) $product->setTranslations('name', $data['name']);
        if (isset($data['description'])) $product->setTranslations('description', $data['description']);
        if (isset($data['short_description'])) $product->setTranslations('short_description', $data['short_description']);
        if (isset($data['details'])) $product->setTranslations('details', $data['details']);

        $product->save();

        // Thumbnail
        if ($request->hasFile('thumbnail')) {
            $this->saveThumbnail($product, $request->file('thumbnail'));
        }

        // Gallery
        if ($request->hasFile('gallery')) {
            $this->saveGallery($product, $request->file('gallery'));
        }

        $product->categories()->sync($data['categories'] ?? []);
        $product->tags()->sync($data['tags'] ?? []);
        $product->attributes()->sync($data['attribute_values'] ?? []);

        return $product;
    }

    private function saveThumbnail(Product $product, $file)
    {
        $fileName = $file->getClientOriginalName();
        $storagePath = "products/{$product->id}/thumbnails/$fileName";
        $file->storeAs("products/{$product->id}/thumbnails", $fileName, 'public');

        if ($product->thumbnail_id) {
            $thumb = Media::find($product->thumbnail_id);
            if ($thumb && Storage::disk('public')->exists($thumb->file_path)) {
                Storage::disk('public')->delete($thumb->file_path);
            }
            $thumb->update(['file_path' => $storagePath]);
        } else {
            $media = Media::create(['file_path' => $storagePath, 'type' => 'image']);
            $product->thumbnail_id = $media->id;
            $product->media()->save($media);
            $product->save();
        }
    }

    private function saveGallery(Product $product, $files)
    {
        foreach ($files as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $fileName = $file->getClientOriginalName();
                $storagePath = "products/{$product->id}/media/$fileName";
                $file->storeAs("products/{$product->id}/media", $fileName, 'public');
                $product->media()->create(['file_path' => $storagePath, 'type' => 'image']);
            }
        }
    }

    private function saveChildren(Product $product, array $children)
    {
        foreach ($children as $childData) {
            $childName = $childData['name'] ?? $product->getTranslations('name');

            $child = $product->children()->create([
                'name' => $childName,
                'sku' => $childData['sku'] ?? null,
                'price' => $childData['price'] ?? 0,
                'sale_price' => $childData['sale_price'] ?? null,
                'currency_id' => $childData['currency_id'] ?? null,
                'quantity' => $childData['quantity'] ?? 0,
                'unit' => $childData['unit'] ?? '',
                'is_active' => $childData['is_active'] ?? true,
            ]);

            $child->setTranslations('name', $product->getTranslations('name'));
            $child->save();

            if (isset($childData['thumbnail']) && $childData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                $this->saveThumbnail($child, $childData['thumbnail']);
            }

            $child->attributes()->sync($childData['attribute_values'] ?? []);
        }
    }

    private function updateChildren(Product $product, array $children)
    {
        $existingIds = $product->children()->pluck('id')->toArray();
        $incomingIds = collect($children)->pluck('id')->filter()->toArray();

        // Delete removed children
        $toDelete = array_diff($existingIds, $incomingIds);
        foreach ($toDelete as $id) {
            $child = Product::find($id);
            if ($child) {
                if ($child->thumbnail_id) {
                    $thumb = Media::find($child->thumbnail_id);
                    Storage::disk('public')->delete($thumb->file_path ?? '');
                    $thumb?->delete();
                }
                $child->delete();
            }
        }

        // Update or create
        foreach ($children as $childData) {
            if (isset($childData['id'])) {
                $child = Product::find($childData['id']);
                if (!$child) continue;

                // Ensure name is set
                $childName = $childData['name'] ?? $child->getTranslations('name');

                $child->fill([
                    'name' => $childName,
                    'sku' => $childData['sku'] ?? null,
                    'price' => $childData['price'] ?? 0,
                    'quantity' => $childData['quantity'] ?? 0,
                    'is_active' => $childData['is_active'] ?? true,
                ])->save();

                if (isset($childData['thumbnail']) && $childData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                    $this->saveThumbnail($child, $childData['thumbnail']);
                }

                $child->attributes()->sync($childData['attribute_values'] ?? []);
            } else {
                $this->saveChildren($product, [$childData]);
            }
        }
    }
}