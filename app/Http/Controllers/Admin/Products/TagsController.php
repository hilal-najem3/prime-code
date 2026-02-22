<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductTag as Tag;
use App\Models\Language;
use App\Http\Requests\Products\Tags\CreateTagRequest;
use App\Http\Requests\Products\Tags\UpdateTagRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return view('admin.products.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        return view('admin.products.tags.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTagRequest $request)
    {
        DB::beginTransaction();
        try {
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->save();
            DB::commit();
            return redirect()->route('admin.product-tags.index')->with('success', 'Tag created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-tags.index')->with('error', 'Failed to create tag.');
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
        $tag = Tag::findOrFail($id);
        return view('admin.products.tags.edit', compact('tag', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $tag = Tag::findOrFail($id);
            $tag->name = $request->name;
            $tag->save();
            DB::commit();
            return redirect()->route('admin.product-tags.index')->with('success', 'Tag updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-tags.index')->with('error', 'Failed to update tag.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $tag = Tag::findOrFail($id);
            // Remove connection with products without deleting them
            $tag->products()->detach();
            $tag->delete();
            DB::commit();
            return redirect()->route('admin.product-tags.index')->with('success', 'Tag deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product-tags.index')->with('error', 'Failed to delete tag.');
        }
    }
}