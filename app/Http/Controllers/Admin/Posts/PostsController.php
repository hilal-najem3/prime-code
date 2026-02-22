<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\Posts\CreatePostRequest;
use App\Http\Requests\Posts\Posts\UpdatePostRequest;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Language;
use App\Models\Media;
use App\Models\PostCategory;
use App\Models\PostTag;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return view('admin.posts.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        $categories = PostCategory::all();
        $tags = PostTag::all();
        return view('admin.posts.posts.create', compact('languages', 'categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePostRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create post without thumbnail initially
            $post = new Post();
            $post->link = $request->link;
            $post->setTranslations('title', $request->title);
            $post->setTranslations('content', $request->content);
            $post->is_active = $request->boolean('is_active', false);
            $post->is_featured = $request->boolean('is_featured', false);
            $post->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = $file->getClientOriginalName();
                $storagePath = "posts/{$post->id}/thumbnails";
                $file->move(public_path("storage/{$storagePath}"), $fileName);

                $media = Media::create([
                    'file_path' => "$storagePath/$fileName",
                    'type' => 'image',
                ]);

                $post->thumbnail_id = $media->id;
                $post->save();

                $post->media()->save($media);
            }

            // === MEDIA IMAGES (gallery) ===
            if ($request->hasFile('gallery') !== null) {
                // Flatten nested array one level
                $files = collect($request->file('gallery'))->flatten(1);
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = $file->getClientOriginalName();
                        $storagePath = "posts/{$post->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $post->media()->save($media);
                    }
                }
            }

            // === RELATIONS ===
            $post->categories()->sync($request->input('categories', []));
            $post->tags()->sync($request->input('tags', []));

            DB::commit();

            return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while creating the post.']);
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
        $categories = PostCategory::all();
        $tags = PostTag::all();
        $post = Post::with(['thumbnail', 'categories', 'tags'])->findOrFail($id);
        $post->media = $post->media()->where('id', '!=', $post->thumbnail->id)->get();
        return view('admin.posts.posts.edit', compact('post', 'languages', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::with(['media', 'tags', 'categories'])->findOrFail($id);

        DB::beginTransaction();
        try {
            // Update main post fields (including translated ones)
            $post->link = $request->link;
            $post->setTranslations('title', $request->title);
            $post->setTranslations('content', $request->content);
            $post->is_active = $request->boolean('is_active', false);
            $post->is_featured = $request->boolean('is_featured', false);
            $post->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail file from storage
                if ($post->thumbnail_id) {
                    $oldThumb = Media::find($post->thumbnail_id);
                    if ($oldThumb && Storage::disk('public')->exists($oldThumb->file_path)) {
                        Storage::disk('public')->delete($oldThumb->file_path);
                    }

                    // Save new thumbnail using move() to preserve filename
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "posts/{$post->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    // Update Media record path
                    $oldThumb->file_path = "$storagePath/$fileName";
                    $oldThumb->save();
                } else {
                    // No existing thumbnail, create new
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "posts/{$post->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    $media = Media::create([
                        'file_path' => "$storagePath/$fileName",
                        'type' => 'image',
                    ]);

                    $post->thumbnail_id = $media->id;
                    $post->media()->save($media);
                    $post->save();
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
                        $storagePath = "posts/{$post->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $post->media()->save($media);
                    }
                }
            }

            // === UPDATE RELATIONS ===
            $post->categories()->sync($request->input('categories', []));
            $post->tags()->sync($request->input('tags', []));

            DB::commit();

            return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while updating the post.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $post = Post::with(['media', 'tags', 'categories'])->findOrFail($id);

            // Delete associated media files from storage
            foreach ($post->media as $media) {
                $filePath = public_path('storage/' . $media->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $media->delete();
            }

            // Delete the entire folder (e.g., posts/{postId})
            $folderPath = "posts/{$post->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            // Detach categories and tags
            $post->categories()->detach();
            $post->tags()->detach();

            // Delete the post
            $post->delete();

            DB::commit();

            return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.posts.index')->with('error', 'Error deleting post: ' . $e->getMessage());
        }
    }
}