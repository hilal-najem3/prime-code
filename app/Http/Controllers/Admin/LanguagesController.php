<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Languages\CreateLanguageRequest;
use App\Http\Requests\Languages\UpdateLanguageRequest;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLanguageRequest $request)
    {
        DB::beginTransaction();
        try {
            $language = Language::create([
                'code' => $request->code,
                'name' => $request->name,
                'is_active' => $request->is_active ? 1 : 0,
            ]);
            DB::commit();
            return redirect()->route('admin.languages.index')->with('success', 'Language created successfully.');
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
        $language = Language::findOrFail($id);
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $language = Language::findOrFail($id);
            $language->update([
                'code' => $request->code,
                'name' => $request->name,
                'is_active' => $request->is_active ? 1 : 0,
            ]);
            DB::commit();
            return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully.');
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
            $language = Language::findOrFail($id);
            $language->delete();
            DB::commit();
            return redirect()->route('admin.languages.index')->with('success', 'Language deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}