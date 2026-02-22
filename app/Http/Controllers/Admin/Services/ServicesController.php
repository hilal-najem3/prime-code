<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\CreateServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Currency;
use App\Models\Media;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.services.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = Currency::all();
        $languages = Language::where('is_active', true)->get();
        return view('admin.services.services.create', compact('currencies', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateServiceRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create the service
            $service = Service::create([
                'name' => $request->name,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'icon' => $request->icon,
                'slug' => $request->slug,
                'price' => $request->price,
                'currency_id' => $request->currency_id,
                'active' => $request->boolean('active', true),
            ]);

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = $file->getClientOriginalName();
                $storagePath = "services/{$service->id}/thumbnails";
                $file->move(public_path("storage/{$storagePath}"), $fileName);

                $media = Media::create([
                    'file_path' => "$storagePath/$fileName",
                    'type' => 'image',
                ]);

                $service->thumbnail_id = $media->id;
                $service->save();

                $service->media()->save($media);
            }

            DB::commit();

            return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
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
        $service = Service::with('media')->findOrFail($id);
        $currencies = Currency::all();
        $languages = Language::where('is_active', true)->get();

        // Ensure the service has a thumbnail
        if (!$service->thumbnail_id) {
            $service->thumbnail_id = null;
        }

        return view('admin.services.services.edit', compact('service', 'currencies', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        DB::beginTransaction();

        try {
            // Update fields
            $service->name = $request->name;
            $service->short_description = $request->short_description;
            $service->description = $request->description;
            $service->icon = $request->icon;
            $service->slug = $request->slug;
            $service->price = $request->price;
            $service->currency_id = $request->currency_id;
            $service->active = $request->boolean('active', true);
            $service->save();

            $mediaDeleted = false;

            // Delete selected media
            if ($request->input('deleted_media') !== null) {
                $ids = explode(',', $request->deleted_media);
                $ids = array_filter($ids, fn($id) => trim($id) !== '');
                $mediaToDelete = Media::whereIn('id', $ids)->get();

                foreach ($mediaToDelete as $media) {
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }

                    if ($service->thumbnail_id == $media->id) {
                        $service->thumbnail_id = null;
                        $service->save();
                    }

                    $media->delete();
                }

                $mediaDeleted = true;
            }

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                // Delete old file if needed
                if ($service->thumbnail_id) {
                    $oldThumb = Media::find($service->thumbnail_id);

                    if (!$mediaDeleted && $oldThumb && Storage::disk('public')->exists($oldThumb->file_path)) {
                        Storage::disk('public')->delete($oldThumb->file_path);
                    }

                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "services/{$service->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    if ($oldThumb) {
                        $oldThumb->file_path = "$storagePath/$fileName";
                        $oldThumb->save();
                    } else {
                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $service->thumbnail_id = $media->id;
                        $service->save();
                        $service->media()->save($media);
                    }
                } else {
                    // No old thumbnail
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "services/{$service->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    $media = Media::create([
                        'file_path' => "$storagePath/$fileName",
                        'type' => 'image',
                    ]);

                    $service->thumbnail_id = $media->id;
                    $service->save();
                    $service->media()->save($media);
                }
            }

            DB::commit();
            return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $service = Service::with('media')->findOrFail($id);

            // Optional: prevent deletion if the service is used in active orders
            if ($service->orders()->exists()) {
                return redirect()->route('admin.services.index')
                    ->with('error', 'Cannot delete a service that is linked to existing orders.');
            }

            // Delete associated media files
            foreach ($service->media as $media) {
                $filePath = public_path('storage/' . $media->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $media->delete();
            }

            // Delete the entire service media folder
            $folderPath = "services/{$service->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            $service->delete();

            DB::commit();
            return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
