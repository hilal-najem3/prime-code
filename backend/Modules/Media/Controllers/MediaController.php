<?php

namespace Modules\Media\Controllers;

use Modules\Media\Models\Media;
use Modules\Media\Services\MediaService;
use Modules\Media\Requests\UploadMediaRequest;
use Modules\Media\Requests\DeleteMediaRequest;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\DB;
use Modules\Media\Requests\AttachMediaRequest;
use Modules\Media\Services\MediaUsageService;
use Modules\Media\Resources\MediaResource;

class MediaController
{
    protected MediaService $mediaService;
    protected MediaUsageService $usageService;

    public function __construct(MediaService $mediaService, MediaUsageService $usageService)
    {
        $this->mediaService = $mediaService;
        $this->usageService = $usageService;
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Media
    |--------------------------------------------------------------------------
    */

    public function upload(UploadMediaRequest $request)
    {
        try {

            DB::beginTransaction();

            $model = null;

            if ($request->model_type && $request->model_id) {
                $modelClass = $request->model_type;

                $model = $modelClass::findOrFail($request->model_id);
            }

            $media = $this->mediaService->upload(
                $request->file('file'),
                $request->directory,
                $request->collection,
                $model,
                user_id()
            );

            if ($request->alt_text) {
                $media->update([
                    'alt_text' => $request->alt_text
                ]);
            }

            DB::commit();

            return ApiResponse::success(
                new MediaResource($media),
                'Media uploaded successfully',
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    public function attach(AttachMediaRequest $request)
    {
        try {

            DB::beginTransaction();

            $media = Media::findOrFail($request->media_id);

            $modelClass = $request->model_type;
            $model = $modelClass::findOrFail($request->model_id);

            $media = $this->mediaService->duplicate(
                $media,
                $model,
                $request->collection
            );

            DB::commit();

            return ApiResponse::success(
                new MediaResource($media),
                'Media attached successfully',
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | List Media
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $query = Media::query();

        if (request()->collection) {
            $query->where('collection', request()->collection);
        }

        if (request()->search) {
            $query->where('filename', 'like', '%' . request()->search . '%');
        }

        $media = $query->latest()->paginate();

        return ApiResponse::success(
            MediaResource::collection($media),
            'Media fetched successfully',
        );
    }

    public function usage($id)
    {
        $media = Media::findOrFail($id);

        $usage = $this->usageService->getUsage($media);

        return ApiResponse::success(
            'Media usage retrieved',
            $usage
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Media
    |--------------------------------------------------------------------------
    */

    public function destroy(DeleteMediaRequest $request)
    {
        try {

            DB::beginTransaction();

            $media = Media::findOrFail($request->media_id);

            $this->mediaService->delete($media);

            DB::commit();

            return ApiResponse::success(
                'Media deleted successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}