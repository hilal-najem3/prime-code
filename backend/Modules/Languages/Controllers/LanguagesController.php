<?php

namespace Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Languages\Models\Language;
use Modules\Languages\Services\LanguageService;
use Modules\Languages\Requests\StoreLanguageRequest;
use Modules\Languages\Requests\UpdateLanguageRequest;
use App\Support\ApiResponse;

class LanguagesController extends Controller
{
    protected LanguageService $service;

    public function __construct(LanguageService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of languages
     */
    public function index()
    {
        $languages = $this->service->getAll();

        return ApiResponse::success(
            data: $languages,
            message: 'Languages fetched successfully'
        );
    }

    /**
     * Store a newly created language
     */
    public function store(StoreLanguageRequest $request)
    {
        try {
            DB::beginTransaction();

            $language = $this->service->create(
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success(
                data: $language,
                message: 'Language created successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified language
     */
    public function show(Language $language)
    {
        return ApiResponse::success(
            data: $language,
            message: 'Language fetched successfully'
        );
    }

    /**
     * Update the specified language
     */
    public function update(UpdateLanguageRequest $request, Language $language)
    {
        try {
            DB::beginTransaction();

            $language = $this->service->update(
                $language,
                $request->validated()
            );

            DB::commit();

            return ApiResponse::success(
                data: $language,
                message: 'Language updated successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified language
     */
    public function destroy(Language $language)
    {
        try {
            DB::beginTransaction();

            $this->service->delete($language);

            DB::commit();

            return ApiResponse::success(
                message: 'Language deleted successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Language $language)
    {
        try {
            DB::beginTransaction();

            $language = $this->service->toggleActive($language);

            DB::commit();

            return ApiResponse::success(
                data: $language,
                message: 'Language status updated successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Set language as default
     */
    public function setDefault(Language $language)
    {
        try {
            DB::beginTransaction();

            $language = $this->service->setDefault($language);

            DB::commit();

            return ApiResponse::success(
                data: $language,
                message: 'Default language updated successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
