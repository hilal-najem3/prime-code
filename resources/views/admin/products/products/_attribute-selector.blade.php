<div x-data="attributeSelector(
        JSON.parse(document.getElementById('attributes-data').textContent),
        '{{ $product->type ?? old('type','simple') }}',
        children[index] // <-- directly use the Alpine child object
    )" x-init="init()" class="mb-4">

    <label class="block font-semibold mb-2">Attributes</label>

    <!-- Hidden inputs -->
    <template x-for="(attr, attrIndex) in child.attribute_values" :key="attrIndex">
        <input type="hidden" :name="`children[${index}][attribute_values][${attrIndex}]`" :value="attr.value_id">
    </template>

    <!-- Selected Attributes -->
    <template x-for="(attr, idx) in selectedAttributes" :key="attr.name">
        <div class="border p-3 mb-2 rounded bg-gray-50">
            <div class="flex justify-between items-center mb-2">
                <span class="font-medium" x-text="attr.name"></span>
                <button type="button" class="text-red-500 text-sm" @click="removeAttribute(idx)">Remove</button>
            </div>

            <select class="w-full border p-2 rounded" x-model="attr.value_id" @change="updateChildAttributes()"
                required>
                <option value="">-- Select Value --</option>
                <template x-for="(label, id) in attr.values" :key="id">
                    <option :value="id" x-text="label"></option>
                </template>
            </select>
        </div>
    </template>

    <!-- Add Attribute -->
    <div class="flex items-center space-x-2 mt-2">
        <select x-model="newAttribute" class="border p-2 rounded flex-1">
            <option value="">-- Select Attribute to Add --</option>
            <template x-for="(values, attrName) in availableAttributes" :key="attrName">
                <option :value="attrName" x-text="attrName"></option>
            </template>
        </select>

        <button type="button" class="bg-blue-600 text-white px-3 py-1 rounded" @click="addAttribute()">Add</button>
    </div>
</div>

<script>
    function attributeSelector(attributesMap = {}, initialType = 'simple', child = null) {
    return {
        attributesMap: attributesMap,        // { attribute_id: { name: 'Size', values: {1:'XS',2:'S'} } }
        selectedAttributes: [],              // array of selected attributes for this child
        newAttribute: '',                    // new attribute to add
        isClassified: initialType === 'classified',

        init() {
            // Initialize selectedAttributes from child.attribute_values
            if (child && Array.isArray(child.attribute_values)) {
            console.log('Initializing attribute selector with child:', child, attributesMap, this.attributesMap, this.selectedAttributes, this.isClassified);
                this.selectedAttributes = child.attribute_values.map(av => {
                    const attrData = attributesMap[av.attribute_id] || {};
                    return {
                        attribute_id: av.attribute_id,
                        name: attrData.name || '',    // for display
                        values: attrData.values || {},
                        value_id: av.value_id
                    };
                });
            }
            this.updateChildAttributes();
        },

        get availableAttributes() {
            const selectedIds = this.selectedAttributes.map(a => a.attribute_id);
            return Object.fromEntries(
                Object.entries(this.attributesMap)
                    .filter(([id, _]) => !selectedIds.includes(Number(id)))
            );
        },

        addAttribute() {
            if (!this.newAttribute) return;

            const attrData = this.attributesMap[this.newAttribute] || {};
            this.selectedAttributes.push({
                attribute_id: Number(this.newAttribute),
                name: attrData.name || '',
                values: attrData.values || {},
                value_id: ''
            });

            this.newAttribute = '';
            this.updateChildAttributes();
        },

        removeAttribute(idx) {
            this.selectedAttributes.splice(idx, 1);
            this.updateChildAttributes();
        },

        updateChildAttributes() {
            if (!child) return;

            child.attribute_values = this.selectedAttributes.map(a => ({
                attribute_id: a.attribute_id,
                value_id: a.value_id
            }));
        }
    }
}
</script>