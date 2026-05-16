<?php

namespace Modules\Blog\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Modules\Blog\Models\Article;
use Modules\Blog\Services\ArticleService;
use Modules\Blog\Requests\StoreArticleRequest;
use Modules\Blog\Requests\UpdateArticleRequest;

/*
|--------------------------------------------------------------------------
| Article Controller
|--------------------------------------------------------------------------
|
| Handles API requests for blog articles.
| Delegates all business logic to ArticleService.
|
*/

class ArticleController extends ApiController
{
    protected ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | List Articles
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $articles = Article::query()
            ->latest()
            ->paginate(15);

        return $this->success(
            $articles,
            'Articles fetched successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Article
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $article = $this->service->find($id);

        return $this->success(
            $article,
            'Article fetched successfully'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Article
    |--------------------------------------------------------------------------
    */

    public function store(StoreArticleRequest $request)
    {
        try {

            DB::beginTransaction();

            $article = $this->service->create(
                $request->validated()
            );

            DB::commit();

            return $this->success(
                $article,
                'Article created successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Article
    |--------------------------------------------------------------------------
    */

    public function update(UpdateArticleRequest $request, $id)
    {
        try {

            DB::beginTransaction();

            $article = $this->service->find($id);
            $article = $this->service->update(
                $article,
                $request->validated()
            );

            DB::commit();

            return $this->success(
                $article,
                'Article updated successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Article
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $article = $this->service->find($id);
            $this->service->delete($article);

            DB::commit();

            return $this->success(
                null,
                'Article deleted successfully'
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}
