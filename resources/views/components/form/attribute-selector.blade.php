@props([ 'label' => 'Attributes', 'attributesData' => [], 'selected' => [], 'productType' => 'simple', ])

<div class="mb-4" x-data="attributeSelector()"
    x-init="init($el, @json($attributesData), @json($selected), '{{ $productType }}')"> <label
        class="block font-semibold mb-2">{{ $label }}</label>

    <template x-for="(attr, idx) in selectedAttributes" :key="idx">
        <div class="border p-3 mb-2 rounded bg-gray-50">
            <div class="flex justify-between items-center mb-2"> <span class="font-medium" x-text="attr.name"></span>
                <button type="button" class="text-red-500 text-sm" @click="removeAttribute(idx)">Remove</button>
            </div>

            <select class="w-full border p-2 rounded" :name="${xName}[${attr.name}]" x-model="attr.value_id" required>
                <option value="">-- Select Value --</option> <template x-for="(label, id) in attr.values" :key="id">
                    <option :value="id" x-text="label"></option>
                </template>
            </select>
        </div>
    </template>

    <div class="flex items-center space-x-2 mt-2"> <select x-model="newAttribute" class="border p-2 rounded flex-1">
            <option value="">-- Select Attribute to Add --</option> <template
                x-for="(values, attrName) in availableAttributes" :key="attrName">
                <option :value="attrName" x-text="attrName"></option>
            </template>
        </select>

        <button type="button" class="bg-blue-600 text-white px-3 py-1 rounded" @click="addAttribute()">Add</button>
    </div>

    <template x-if="isClassified && selectedAttributes.length === 0">
        <p class="text-red-500 text-sm mt-2">At least one attribute is required for classified products.</p>
    </template>
</div>

<script>
    function attributeSelector() { return { xName: 'attribute_values', // safe default attributesData: {}, // { "Size": { "1":"XS", ... }, ... } selectedAttributes: [], // [{ name, value_id, values }] newAttribute: '', productType: 'simple',

init(el, attributesData = {}, selected = {}, productType = 'simple') { // ensure we have a plain object this.attributesData = attributesData && typeof attributesData === 'object' ? attributesData : {}; this.productType = productType || 'simple';

// If parent stored a custom xName on element, pick it up if (el && el._xInitXName) { this.xName = el._xInitXName; }

// initialize selected attributes // selected may be: { "Size": 2, "Quality": 9 } OR array; handle object case if (selected && typeof selected === 'object' && !Array.isArray(selected)) { for (const [attrName, valueId] of Object.entries(selected)) { if (this.attributesData[attrName]) { this.selectedAttributes.push({ name: attrName, value_id: valueId ?? '', values: this.attributesData[attrName], }); } } }

// defensive: if selected is an array of objects (e.g., from children), normalize if (Array.isArray(selected)) { selected.forEach(s => { if (s.name && this.attributesData[s.name]) { this.selectedAttributes.push({ name: s.name, value_id: s.value_id ?? '', values: this.attributesData[s.name], }); } }); } },

setXName(el, name) { el._xInitXName = name; this.xName = name; },

get availableAttributes() { const used = this.selectedAttributes.map(a => a.name); const entries = Object.entries(this.attributesData).filter(([key]) => !used.includes(key)); // return as an object so x-for can iterate return Object.fromEntries(entries); },

get isClassified() { return this.productType === 'classified'; },

addAttribute() { if (!this.newAttribute) return; const values = this.attributesData[this.newAttribute]; if (!values) return; // push a new selected attribute with values so the values dropdown appears this.selectedAttributes.push({ name: this.newAttribute, value_id: '', values: values, }); this.newAttribute = ''; },

removeAttribute(index) { this.selectedAttributes.splice(index, 1); } } } 
</script>