<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Attributes\CreateAttributeRequest;
use App\Http\Requests\Products\Attributes\UpdateAttributeRequest;
use Illuminate\Support\Facades\DB;
use App\Models\AttributeValue;
use App\Models\Attribute;
use App\Models\Language;
use Exception;
use Illuminate\Support\Facades\Log;

class AttributesController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();
        return view('admin.products.attributes.index', compact('attributes'));
    }

    public function create()
    {
        $languages = Language::where('is_active', 1)->get();
        return view('admin.products.attributes.create', compact('languages'));
    }

    public function store(CreateAttributeRequest $request)
    {
        DB::beginTransaction();

        try {
            $attribute = new Attribute($request->only([
                'type',
                'is_required',
                'is_filterable',
                'sort_order',
            ]));
            $attribute->setTranslations('name', $request->name);
            $attribute->save();

            // Create attribute values if provided
            if ($request->has('values') && is_array($request->values)) {
                foreach ($request->values as $valueData) {
                    if (!empty($valueData['value'])) {
                        $value = new AttributeValue([
                            'color' => $valueData['color'] ?? null,
                        ]);
                        $translations = is_array($valueData['value']) ? $valueData['value'] : [];
                        $value->setTranslations('value', $translations);
                        $attribute->values()->save($value);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while creating the attribute.']);
        }
    }

    public function edit(string $id)
    {
        $languages = Language::where('is_active', 1)->get();
        $attribute = Attribute::with(['values'])->findOrFail($id);
        return view('admin.products.attributes.edit', compact('attribute', 'languages',));
    }

    public function update(UpdateAttributeRequest $request, $id)
    {
        $attribute = Attribute::with(['values'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $attribute->setTranslations('name', $request->name);
            $attribute->fill($request->only([
                'type',
                'is_required',
                'is_filterable',
                'sort_order',
            ]));
            $attribute->save();

            // Handle values
            $existingValueIds = [];
            if ($request->has('values') && is_array($request->values)) {
                foreach ($request->values as $index => $valueData) {
                    if (!empty($valueData['value'])) {
                        // Find existing value by index/id if available
                        $value = $attribute->values[$index] ?? new AttributeValue(['attribute_id' => $attribute->id]);
                        $value->color = $valueData['color'] ?? null;

                        $translations = is_array($valueData['value']) ? $valueData['value'] : [];
                        $value->setTranslations('value', $translations);
                        $value->save();

                        $existingValueIds[] = $value->id;
                    }
                }
            }

            // Delete removed values
            $attribute->values()
                ->whereNotIn('id', $existingValueIds)
                ->delete();

            DB::commit();
            return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'An error occurred while updating the attribute.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $attribute = Attribute::findOrFail($id);

            $attribute->values()->each(function ($value) {
                $value->products()->detach();
            });
            $attribute->values()->delete();
            $attribute->delete();

            DB::commit();

            return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting Attribute: ' . $e->getMessage());
            DB::rollBack();

            return redirect()->route('admin.attributes.index')->with('error', 'Error deleting Attribute: ' . $e->getMessage());
        }
    }
}