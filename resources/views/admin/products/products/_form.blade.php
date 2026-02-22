@php
$isEdit = isset($product);
@endphp

<script type="application/json" id="attributes-data">
    {!! json_encode($attributesMap) !!}
</script>

@if($isEdit)
<script type="application/json" id="product-data">
    {!! json_encode($product->toFrontend(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif

<form method="POST"
    action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
    enctype="multipart/form-data" x-data="productForm()"
    x-init="init({{ $isEdit ? 'JSON.parse(document.getElementById(\'product-data\').textContent)' : 'null' }})">

    @csrf
    @if ($isEdit)
    @method('PUT')
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <x-ui.toast :message="$error" :color="'red'" />
    @endforeach
    @endif

    {{-- Product Type --}}
    <x-form.select name="type" label="Product Type" :options="['simple'=>'Simple','classified'=>'Classified']" required
        x-model="type" :selected="old('type', $product?->type ?? 'simple')" />

    {{-- Name (Multilang) --}}
    <x-form.input-multilang label="Name" name="name" :languages="$languages"
        :value="old('name', $product?->getTranslations('name') ?? [])" required />

    {{-- Short Description --}}
    <x-form.translatable-editor-table label="Short Description" name="short_description" :languages="$languages"
        :value="old('short_description', $product?->getTranslations('short_description') ?? [])" />

    {{-- Description --}}
    <x-form.translatable-editor-table label="Description" name="description" :languages="$languages"
        :value="old('description', $product?->getTranslations('description') ?? [])" />

    {{-- Details --}}
    <x-form.translatable-editor-table label="Details" name="details" :languages="$languages"
        :value="old('details', $product?->getTranslations('details') ?? [])" />

    {{-- Pricing & Inventory (hidden if classified) --}}
    <template x-if="type === 'simple'">
        <div class="mt-3">
            <x-form.input label="SKU" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required />
            <x-form.input label="Price" name="price" type="number" step="0.01"
                value="{{ old('price', $product->price ?? '') }}" required />
            <x-form.input label="Sale Price" name="sale_price" type="number" step="0.01"
                value="{{ old('sale_price', $product->sale_price ?? '') }}" />
            <x-form.select label="Currency" name="currency_id" :options="$currencies->pluck('code', 'id')"
                :selected="old('currency_id', $product->currency_id ?? '')" required />
            <x-form.input label="Quantity" name="quantity" type="number"
                value="{{ old('quantity', $product->quantity ?? 0) }}" required />
            <x-form.input label="Unit" name="unit" value="{{ old('unit', $product->unit ?? '') }}" required />
            <x-form.sort-order :value="$product->sort_order ?? 0" />
        </div>
    </template>

    {{-- Thumbnail --}}
    <x-form.image label="Thumbnail" name="thumbnail" :multiple="false"
        :existing="isset($product) && $product->thumbnail ? [$product->thumbnail] : []" />

    {{-- Gallery --}}
    <x-form.image label="Gallery Images" name="gallery[]" :multiple="true"
        :existing="isset($product) ? $product->media->where('collection_name', 'gallery') : []" />

    {{-- Hidden input to preserve existing gallery images --}}
    @if($isEdit)
    @foreach($product->media->where('collection_name', 'gallery') as $image)
    <input type="hidden" name="existing_gallery_images[]" value="{{ $image->id }}">
    @endforeach
    @endif

    {{-- Categories --}}
    @if($isEdit)
    <x-form.select-box label="Categories" name="categories" :options="$categories->pluck('name', 'id')"
        :selected="old('categories', $product?->categories->pluck('id')->toArray() ?? [])" :multiSelect="true" />
    @else
    <x-form.select-box label="Categories" name="categories" :options="$categories->pluck('name', 'id')"
        :selected="old('categories', [])" :multiSelect="true" />
    @endif

    {{-- Tags --}}
    @if($isEdit)
    <x-form.select-box label="Tags" name="tags" :options="$tags->pluck('name', 'id')"
        :selected="old('tags', $product?->tags->pluck('id')->toArray() ?? [])" :multiSelect="true" />
    @else
    <x-form.select-box label="Tags" name="tags" :options="$tags->pluck('name', 'id')" :selected="old('tags', [])"
        :multiSelect="true" />
    @endif

    {{-- Status --}}
    <x-form.select label="Status" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']"
        :selected="old('is_active', $product->is_active ?? '1')" />

    {{-- Featured --}}
    <x-form.select label="Featured" name="is_featured" :options="['1' => 'Featured', '0' => 'Not Featured']"
        :selected="old('is_featured', $product->is_featured ?? '0')" />

    <div x-data="childrenForm(
            JSON.parse(document.getElementById('attributes-data').textContent),
            document.getElementById('product-data')
                ? JSON.parse(document.getElementById('product-data').textContent).children
                : []
        )" x-init="initChildren()" x-show="type === 'classified'" class="mt-6">

        <h3 class="font-bold mb-2">Children</h3>

        <template x-for="(child, index) in children" :key="index">
            <div class="border p-4 mb-4 relative">
                <button type="button" @click="removeChild(index)"
                    class="absolute top-2 right-2 text-red-500">Remove</button>

                <input type="hidden" :name="`children[${index}][id]`" x-model="child.id">
                <x-form.input-multilang label="Child Name" x_name="'children[' + index + '][name][{locale}]'"
                    x_model="child.name" :languages="$languages" required />
                <x-form.input x_name="`children[${index}][sku]`" x_model="child.sku" label="SKU" required />
                <x-form.input x_name="`children[${index}][price]`" x_model="child.price" type="number" step="0.01"
                    label="Price" required />
                <x-form.input x_name="`children[${index}][sale_price]`" x_model="child.sale_price" type="number"
                    step="0.01" label="Sale Price" />
                <x-form.select x_name="`children[${index}][currency_id]`" label="Currency"
                    :options="\App\Models\Currency::pluck('name','id')->toArray()" x-model="child.currency_id"
                    required />
                <x-form.input x_name="`children[${index}][quantity]`" type="number" label="Quantity"
                    x-model="child.quantity" required />
                <x-form.input x_name="`children[${index}][unit]`" label="Unit" x-model="child.unit" required />
                <x-form.checkbox x_name="`children[${index}][is_active]`" label="Active" x-model="child.is_active" />
                <x-form.alpine-image label="Child Thumbnail" x_name="'children[' + index + '][thumbnail]'"
                    x_model="child.thumbnail" :multiple="false"
                    x-bind:existing="child.thumbnail_url ? [{ id: child.id, url: child.thumbnail_url }] : []" />
                @include('admin.products.products._attribute-selector', [
                'index' => 'index' // pass the loop index
                ])
            </div>
        </template>

        <button type="button" @click="addChild()" class="btn btn-primary mt-2">Add Child</button>
    </div>

    {{-- Submit --}}
    <div class="flex justify-between pt-4">
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">← Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Product' : 'Create Product' }}
        </button>
    </div>
</form>

@push('scripts')
{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.editor').forEach(function (textarea) {
            CKEDITOR.replace(textarea.id, {
                height: 300,
                removeButtons: '',
                allowedContent: true
            });
        });

        const typeSelect = document.querySelector('[name=type]');
        const childrenDiv = document.querySelector('[x-data="childrenForm()"]');
        
        const toggleChildren = () => {
        if (typeSelect.value === 'classified') {
        childrenDiv.style.display = 'block';
        } else {
        childrenDiv.style.display = 'none';
        }
        };
        
        toggleChildren();
        typeSelect.addEventListener('change', toggleChildren);
    });
</script>
<script>
    function productForm() {
        return {
            type: 'simple',
            init(product) {
                if(product && product.type) this.type = product.type;
                console.log('Initialized product form with type:', this.type, product);
            }
        }
    }
</script>
<script>
    function childrenForm(attributesMap = {}, initialChildren = []) {
    return {
        attributesMap,
        children: [],

        initChildren() {
            this.children = Array.isArray(initialChildren)
            ? initialChildren.map(c => ({
            ...c,
            is_active: Boolean(c.is_active ?? true), // ensure true/false
            thumbnail: null,
            thumbnail_url: c.thumbnail_url || null, // this is what existingFiles uses
            attribute_values: c.attribute_values || [], // ensure attributes key exists
            name: c.name || {} // initialize multilingual name
            }))
            : [];
        },

        addChild() {
            this.children.push({
                id: null,
                sku: '',
                price: '',
                sale_price: '',
                currency_id: '',
                quantity: 0,
                unit: '',
                is_active: true,
                thumbnail: null,
                attribute_values: [],
            });
        },

        removeChild(index) {
            this.children.splice(index, 1);
        },
    };
}
</script>
@endpush