<script setup lang="ts">
import { ref, computed, watch } from "vue";

type Option = {
  label: string;
  value: string | number;
};

type ModelValue = string | number | (string | number)[] | null;

const props = defineProps<{
  modelValue: ModelValue;
  options?: Option[];

  placeholder?: string;
  multiple?: boolean;
  searchable?: boolean;

  fetch?: (query: string) => Promise<Option[]>; // 🔥 async mode
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: ModelValue): void;
}>();

const open = ref(false);
const search = ref("");
const loading = ref(false);
const internalOptions = ref<Option[]>(props.options || []);

let debounceTimer: any = null;

/**
|--------------------------------------------------------------------------
| 🔍 Fetch Logic (Async Mode)
|--------------------------------------------------------------------------
*/
const runFetch = async (query: string) => {
  if (!props.fetch) return;

  loading.value = true;

  try {
    const result = await props.fetch(query);
    internalOptions.value = result;
  } finally {
    loading.value = false;
  }
};

watch(search, (val) => {
  if (!props.fetch) return;

  clearTimeout(debounceTimer);

  debounceTimer = setTimeout(() => {
    runFetch(val);
  }, 300);
});

/**
|--------------------------------------------------------------------------
| 📦 Options Source
|--------------------------------------------------------------------------
*/
const resolvedOptions = computed(() => {
  return props.fetch ? internalOptions.value : props.options || [];
});

/**
|--------------------------------------------------------------------------
| ✅ Selection Logic
|--------------------------------------------------------------------------
*/
const isSelected = (option: Option) => {
  if (props.multiple) {
    return (
      Array.isArray(props.modelValue) && props.modelValue.includes(option.value)
    );
  }
  return props.modelValue === option.value;
};

const toggleOption = (option: Option) => {
  if (props.multiple) {
    const current = Array.isArray(props.modelValue)
      ? [...props.modelValue]
      : [];

    const index = current.indexOf(option.value);

    if (index > -1) current.splice(index, 1);
    else current.push(option.value);

    emit("update:modelValue", current);
  } else {
    emit("update:modelValue", option.value);
    open.value = false;
  }
};

/**
|--------------------------------------------------------------------------
| 🔍 Local Filter (only if no fetch)
|--------------------------------------------------------------------------
*/
const filteredOptions = computed(() => {
  if (props.fetch) return resolvedOptions.value;

  if (!props.searchable || !search.value) return resolvedOptions.value;

  return resolvedOptions.value.filter((o) =>
    o.label.toLowerCase().includes(search.value.toLowerCase()),
  );
});

/**
|--------------------------------------------------------------------------
| 🏷 Selected Label
|--------------------------------------------------------------------------
*/
const selectedLabel = computed(() => {
  if (props.multiple) {
    if (!Array.isArray(props.modelValue) || !props.modelValue.length)
      return props.placeholder || "Select...";

    return resolvedOptions.value
      .filter(
        (o) =>
          Array.isArray(props.modelValue) && props.modelValue.includes(o.value),
      )
      .map((o) => o.label)
      .join(", ");
  }

  const found = resolvedOptions.value.find((o) => o.value === props.modelValue);

  return found?.label || props.placeholder || "Select...";
});

/**
|--------------------------------------------------------------------------
| ❌ Close Dropdown
|--------------------------------------------------------------------------
*/
const close = () => {
  open.value = false;
  search.value = "";
};
</script>

<template>
  <div class="relative w-full">
    <!-- Trigger -->
    <div
      @click="open = !open"
      class="w-full px-4 py-2 rounded-lg cursor-pointer bg-bg-primary border border-border text-text-primary flex justify-between items-center"
    >
      <span class="truncate">{{ selectedLabel }}</span>
      <span class="text-text-secondary">▼</span>
    </div>

    <!-- Dropdown -->
    <div
      v-if="open"
      class="absolute z-50 mt-2 w-full rounded-lg bg-surface-primary border border-border shadow-lg"
    >
      <!-- Search -->
      <div v-if="searchable" class="p-2 border-b border-border">
        <input
          v-model="search"
          type="text"
          placeholder="Search..."
          class="w-full px-3 py-1 rounded-md bg-bg-primary border border-border text-text-primary"
        />
      </div>

      <!-- Options -->
      <div class="max-h-60 overflow-auto">
        <!-- Loading -->
        <div v-if="loading" class="px-4 py-2 text-text-secondary">
          Loading...
        </div>

        <!-- Options -->
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          @click="toggleOption(option)"
          class="px-4 py-2 cursor-pointer flex justify-between items-center hover:bg-bg-secondary"
        >
          <span>{{ option.label }}</span>

          <span v-if="isSelected(option)">✔</span>
        </div>

        <!-- Empty -->
        <div
          v-if="!loading && !filteredOptions.length"
          class="px-4 py-2 text-text-secondary"
        >
          No results
        </div>
      </div>
    </div>
  </div>
</template>
