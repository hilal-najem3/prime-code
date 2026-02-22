<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Media;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['thumbnail'])->get(); // Eager load the thumbnail relationship']);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all(); // Get all roles
        return view('admin.users.create', compact('roles'));
    }

    public function store(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'active' => $request->active,
            ]);

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = $file->getClientOriginalName();
                $storagePath = "users/{$user->id}/thumbnails";
                $file->move(public_path("storage/{$storagePath}"), $fileName);

                $media = Media::create([
                    'file_path' => "$storagePath/$fileName",
                    'type' => 'image',
                ]);

                $user->thumbnail_id = $media->id;
                $user->save();
                $user->media()->save($media);
            }

            // Attach roles if provided
            if ($request->filled('roles')) {
                $user->roles()->sync($request->roles);
            }
            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
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
        $roles = Role::all(); // Get all roles
        $user = User::with(['thumbnail', 'roles', 'media'])->findOrFail($id); // Find the user by ID
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            // Update user fields
            $user->name = $request->name;
            $user->email = $request->email;
            $user->active = $request->active;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $mediaDeleted = false;
            if ($request->input('deleted_media') !== null) {
                $ids = explode(',', $request->deleted_media);
                $ids = array_filter($ids, fn($id) => trim($id) !== '');
                $mediaToDelete = Media::whereIn('id', $ids)->get();
                foreach ($mediaToDelete as $media) {
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                    if ($user->thumbnail_id == $media->id) {
                        $user->thumbnail_id = null;
                        $user->save();
                    }
                    $media->delete();
                }
            }

            // === THUMBNAIL ===
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail file from storage
                if ($user->thumbnail_id) {
                    $oldThumb = Media::find($user->thumbnail_id);

                    if (!$mediaDeleted) {
                        if ($oldThumb && Storage::disk('public')->exists($oldThumb->file_path)) {
                            Storage::disk('public')->delete($oldThumb->file_path);
                        }
                    }

                    // Save new thumbnail using move() to preserve filename
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "users/{$user->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    if ($oldThumb) {
                        // Update Media record path
                        $oldThumb->file_path = "$storagePath/$fileName";
                        $oldThumb->save();
                    } else {

                        $media = Media::create([
                            'file_path' => "$storagePath/$fileName",
                            'type' => 'image',
                        ]);

                        $user->thumbnail_id = $media->id;
                        $user->save();
                        $user->media()->save($media);
                    }
                } else {
                    // No existing thumbnail, create new
                    $file = $request->file('thumbnail');
                    $fileName = $file->getClientOriginalName();
                    $storagePath = "users/{$user->id}/thumbnails";
                    $file->move(public_path("storage/{$storagePath}"), $fileName);

                    $media = Media::create([
                        'file_path' => "$storagePath/$fileName",
                        'type' => 'image',
                    ]);

                    $user->thumbnail_id = $media->id;
                    $user->save();
                    $user->media()->save($media);
                }
            }

            // Sync roles if provided
            if ($request->filled('roles')) {
                $user->roles()->sync($request->roles);
            } else {
                // Optionally detach all roles if none selected
                $user->roles()->detach();
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
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
            $user = User::with(['media'])->findOrFail($id);

            // Check if user has 'admin' role
            if ($user->roles()->where('name', 'admin')->exists()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Cannot delete an admin user.');
            }

            // Delete associated media files from storage
            foreach ($user->media as $media) {
                $filePath = public_path('storage/' . $media->file_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                $media->delete();
            }

            // Delete the entire folder (e.g., users/{userId})
            $folderPath = "users/{$user->id}";
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}