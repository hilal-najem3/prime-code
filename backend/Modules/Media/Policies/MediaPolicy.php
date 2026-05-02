<?php

namespace Modules\Media\Policies;

use Illuminate\Auth\Access\Response;
use Modules\Auth\Models\User;
use Modules\Media\Models\Media;

class MediaPolicy
{
    public static string $model = \Modules\Media\Models\Media::class;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Media $media): bool
    {
        // Owner
        if ($media->user_id === $user->id) {
            return true;
        }

        // Same model owner (example: patient doctor)
        if ($media->model_type && $media->model_id) {
            // Customize per domain (VERY IMPORTANT)
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Media $media): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Media $media): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Media $media): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Media $media): bool
    {
        return false;
    }
}