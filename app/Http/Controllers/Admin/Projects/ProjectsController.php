<?php

namespace App\Http\Controllers\Admin\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\Projects\CreateProjectRequest;
use App\Http\Requests\Projects\Projects\UpdateProjectRequest;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Language;
use App\Models\Media;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('admin.projects.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        $categories = ProjectCategory::all();
        $tags = ProjectTag::all();
        return view('admin.projects.projects.create', compact('languages', 'categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProjectRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create Project without thumbnail initially
            $project = new Project();
            $project->link = $request->link;
            $project->setTranslations('title', $request->title);
            $project->setTranslations('content', $request->content);
            $project->is_active = $request->boolean('is_active', false);
            $project->is_featured = $request->boolean('is_featured', false);
            $project->links = $request->input('links', []); // assuming it's an array
            $project->sort_order = $request->input('sort_order', 0);
            $project->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = $file->getClientOriginalName();
                $storagePath = "projects/{$project->id}/thumbnails";
                $file->move(public_path("storage/{$storagePath}"), $fileName);

                $media = Media::create([
                    'file_path' => "$storagePath/$fileName",
                    'type' => 'image',
                ]);

                $project->thumbnail_id = $media->id;
                $project->save();

                $project->media()->save($media);
            }

            // === MEDIA IMAGES (gallery) ===
            if ($request->hasFile('gallery') !== null) {
                // Flatten nested array one level
                $files = collect($request->file('gallery'))->flatten(1);
                foreach ($files as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $fileName = $file->getClientOriginalName();
                        $storagePath = "projects/{$project->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $project->media()->save($media);
                    }
                }
            }

            // === RELATIONS ===
            $project->categories()->sync($request->input('categories', []));
            $project->tags()->sync($request->input('tags', []));

            DB::commit();

            return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while creating the Project.']);
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
        $categories = ProjectCategory::all();
        $tags = ProjectTag::all();
        $project = Project::with(['thumbnail', 'categories', 'tags'])->findOrFail($id);
        $project->media = $project->media()->where('id', '!=', $project->thumbnail->id)->get();
        return view('admin.projects.projects.edit', compact('project', 'languages', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::with(['media', 'tags', 'categories'])->findOrFail($id);

        DB::beginTransaction();
        try {
            // Update main Project fields (including translated ones)
            $project->link = $request->link;
            $project->setTranslations('title', $request->title);
            $project->setTranslations('content', $request->content);
            $project->is_active = $request->boolean('is_active', false);
            $project->is_featured = $request->boolean('is_featured', false);
            $project->links = $request->input('links', []);
            $project->sort_order = $request->input('sort_order', 0);
            $project->save();

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail file from storage
                if ($project->thumbnail_id) {
                    $oldThumb = Media::find($project->thumbnail_id);
                    if ($oldThumb && Storage::disk('public')->exists($oldThumb->file_path)) {
                        Storage::disk('public')->delete($oldThumb->file_path);
                    }

                    // Save new thumbnail using move() to preserve filename
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "projects/{$project->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    // Update Media record path
                    $oldThumb->file_path = "$storagePath/$fileName";
                    $oldThumb->save();
                } else {
                    // No existing thumbnail, create new
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "projects/{$project->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    $media = Media::create([
                        'file_path' => "$storagePath/$fileName",
                        'type' => 'image',
                    ]);

                    $project->thumbnail_id = $media->id;
                    $project->media()->save($media);
                    $project->save();
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
                        $storagePath = "projects/{$project->id}/media";
                        $file->move(public_path("storage/{$storagePath}"), $fileName);

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $project->media()->save($media);
                    }
                }
            }

            // === UPDATE RELATIONS ===
            $project->categories()->sync($request->input('categories', []));
            $project->tags()->sync($request->input('tags', []));

            DB::commit();

            return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while updating the Project.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $project = Project::with(['media', 'tags', 'categories'])->findOrFail($id);

            // Delete associated media files from storage
            foreach ($project->media as $media) {
                $filePath = public_path('storage/' . $media->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $media->delete();
            }

            // Delete the entire folder (e.g., projects/{ProjectId})
            $folderPath = "projects/{$project->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            // Detach categories and tags
            $project->categories()->detach();
            $project->tags()->detach();

            // Delete the Project
            $project->delete();

            DB::commit();

            return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.projects.index')->with('error', 'Error deleting Project: ' . $e->getMessage());
        }
    }
}