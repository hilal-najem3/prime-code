<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory as Category;
use App\Models\Language;
use App\Http\Requests\Products\Categories\CreateCategoryRequest;
use App\Http\Requests\Products\Categories\UpdateCategoryRequest;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['parent'])->get()->each(function ($category) {
            // Decode current category name
            $category->name = json_decode($category->name, true);

            // Handle parent name safely
            if (is_null($category->parent_id)) {
                $category->parent = null;
                $category->parent_name = null;
            } else {
                $parentName = $category->parent->toArray()['name'];
                $category->parent_name = is_array($parentName) ? ($parentName['en'] ?? reset($parentName)) : null;
            }
        });

        return view('admin.products.categories.index', compact('categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        $categories = Category::all();
        return view('admin.products.categories.create', compact('languages', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $category = new Category();
            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? null; // Handle optional parent_id
            $category->is_active = $request->boolean('is_active', false);
            $category->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = $file->getClientOriginalName();
                $storagePath = "product_categories/{$category->id}/thumbnails";
                $file->move(public_path("storage/{$storagePath}"), $fileName);

                $media = Media::create([
                    'file_path' => "$storagePath/$fileName",
                    'type' => 'image',
                ]);

                $category->thumbnail_id = $media->id;
                $category->save();

                $category->media()->save($media);
            }

            // === MEDIA IMAGES (gallery) ===
            if ($request->hasFile('gallery') !== null) {
                // Flatten nested array one level
                $files = collect($request->file('gallery'))->flatten(1);
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = $file->getClientOriginalName();
                        $storagePath = "product_categories/{$category->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $category->media()->save($media);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.product-categories.index')->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-categories.index')->with('error', 'Failed to create category.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $languages = Language::where('is_active', 1)->get();
        $category = Category::findOrFail($id);
        $categories = Category::with(['thumbnail', 'parent'])->where('id', '!=', $id)->get(); // exclude self
        $category->media = $category->media()->where('id', '!=', $category->thumbnail->id)->get();
        return view('admin.products.categories.edit', compact('category', 'languages', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? null; // Handle optional parent_id
            $category->is_active = $request->boolean('is_active', false);
            $category->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail file from storage
                if ($category->thumbnail_id) {
                    $oldThumb = Media::find($category->thumbnail_id);
                    if ($oldThumb && Storage::disk('public')->exists($oldThumb->file_path)) {
                        Storage::disk('public')->delete($oldThumb->file_path);
                    }

                    // Save new thumbnail using move() to preserve filename
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "product_categories/{$category->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    // Update Media record path
                    $oldThumb->file_path = "$storagePath/$fileName";
                    $oldThumb->save();
                } else {
                    // No existing thumbnail, create new
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "product_categories/{$category->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    $media = Media::create([
                        'file_path' => "$storagePath/$fileName",
                        'type' => 'image',
                    ]);

                    $category->thumbnail_id = $media->id;
                    $category->media()->save($media);
                    $category->save();
                }
            }

            // === DELETE OLD MEDIA ===
            if ($request->deleted_media !== null) {
                $ids = explode(',', $request->deleted_media);
                $ids = array_filter($ids, fn($id) => trim($id) !== '');
                $mediaToDelete = Media::whereIn('id', $ids)->get();

                foreach ($mediaToDelete as $media) {
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                    $media->delete();
                }
            }

            // === MEDIA IMAGES (gallery) ===
            if ($request->hasFile('gallery') !== null) {
                // Flatten nested array one level
                $files = collect($request->file('gallery'))->flatten(1);
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = $file->getClientOriginalName();
                        $storagePath = "product_categories/{$category->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $category->media()->save($media);
                    }
                }
            }


            DB::commit();
            return redirect()->route('admin.product-categories.index')->with('success', 'Category updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-categories.index')->with('error', 'Failed to update category.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::with(['media'])->findOrFail($id);
            // Remove connection with products without deleting them
            $category->products()->detach();

            // Delete associated media files from storage
            foreach ($category->media as $media) {
                $filePath = public_path('storage/' . $media->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $media->delete();
            }

            // Delete the entire folder (e.g., product_categories/{categotyId})
            $folderPath = "product_categories/{$category->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            $category->delete();
            DB::commit();
            return redirect()->route('admin.product-categories.index')->with('success', 'Category deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-categories.index')->with('error', 'Failed to delete category.');
        }
    }
}