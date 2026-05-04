<?php

namespace Modules\Pages\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Pages\Models\Page;
use Modules\Pages\Services\PageService;
use Modules\Pages\Requests\StorePageRequest;
use Modules\Pages\Requests\UpdatePageRequest;
use App\Support\ApiResponse;

/*
|--------------------------------------------------------------------------
| Pages Controller
|--------------------------------------------------------------------------
|
| Handles API endpoints for pages.
| Business logic is delegated to PageService.
|
*/

class PagesController extends Controller
{
    protected PageService $service;

    public function __construct(PageService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Pages
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $pages = $this->service->getAll();

        return ApiResponse::success($pages, 'Pages fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Page
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $page = $this->service->find($id);

        return ApiResponse::success($page, 'Page fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Page
    |--------------------------------------------------------------------------
    */

    public function store(StorePageRequest $request)
    {
        try {

            DB::beginTransaction();

            $page = $this->service->create($request->validated());

            DB::commit();

            return ApiResponse::success($page, 'Page created successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Page
    |--------------------------------------------------------------------------
    */

    public function update(UpdatePageRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $page = $this->service->find($id);
            $page = $this->service->update($page, $request->validated());

            DB::commit();

            return ApiResponse::success($page, 'Page updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Page
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $page = $this->service->find($id);
            $this->service->delete($page);

            DB::commit();

            return ApiResponse::success(null, 'Page deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }
}
