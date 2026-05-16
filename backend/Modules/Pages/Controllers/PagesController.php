<?php

namespace Modules\Pages\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Modules\Pages\Models\Page;
use Modules\Pages\Services\PageService;
use Modules\Pages\Requests\StorePageRequest;
use Modules\Pages\Requests\UpdatePageRequest;

/*
|--------------------------------------------------------------------------
| Pages Controller
|--------------------------------------------------------------------------
|
| Handles API endpoints for pages.
| Business logic is delegated to PageService.
|
*/

class PagesController extends ApiController
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

        return $this->success($pages, 'Pages fetched successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Show Page
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $page = $this->service->find($id);

        return $this->success($page, 'Page fetched successfully');
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

            return $this->success($page, 'Page created successfully');
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

            return $this->success($page, 'Page updated successfully');
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

            return $this->success(null, 'Page deleted successfully');
        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }
}
