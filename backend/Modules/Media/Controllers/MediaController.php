<?php

namespace Modules\Media\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Media\Models\Media;
use Modules\Media\Services\MediaService;
use Modules\Media\Requests\UploadMediaRequest;
use Modules\Media\Requests\DeleteMediaRequest;
use Illuminate\Support\Facades\DB;
use Modules\Media\Requests\AttachMediaRequest;
use Modules\Media\Services\MediaUsageService;
use Modules\Media\Resources\MediaResource;

class MediaController extends ApiController
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
                user_id(),
                $request->input('disk')
            );

            if ($request->alt_text) {
                $media->update([
                    'alt_text' => $request->alt_text
                ]);
            }

            DB::commit();

            return $this->success(
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

            return $this->success(
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
        $media = $this->mediaService->list([
            'collection' => request('collection'),
            'search'     => request('search'),
            'per_page'   => request('per_page'),
        ]);

        return $this->success(
            MediaResource::collection($media),
            'Media fetched successfully'
        );
    }

    public function secure($id)
    {
        // if (!request()->hasValidSignature()) {
        //     abort(403);
        // }

        $media = $this->mediaService->findOrFail($id);

        // $this->authorize('view', $media);

        return $this->mediaService->getFilePreviewStream($media);
    }

    public function usage($id)
    {
        $media = $this->mediaService->findOrFail($id);

        $usage = $this->mediaService->usage($media);

        return $this->success(
            $usage,
            'Media usage retrieved'
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

            return $this->success(
                'Media deleted successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
