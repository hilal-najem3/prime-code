<script setup lang="ts">
// path to: frontend/packages/media/components/MediaSelect.vue
import { SelectInput } from "@ui";
import { mediaApi } from "@media";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  modelValue: number | number[] | null;
  multiple?: boolean;
  collection?: string;

  placeholder?: string;
}>();

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/
const emit = defineEmits<{
  (e: "update:modelValue", value: any): void;
}>();

/*
|--------------------------------------------------------------------------
| Fetch Media
|--------------------------------------------------------------------------
*/
const fetchMedia = async (query: string) => {
  const res = await mediaApi.list({
    search: query,
    collection: props.collection,
  });

  return res.data.map((m: any) => ({
    label: m.filename,
    value: m.id,
  }));
};
</script>

<template>
  <SelectInput
    :modelValue="modelValue"
    @update:modelValue="emit('update:modelValue', $event)"
    :multiple="multiple"
    searchable
    :fetch="fetchMedia"
    :placeholder="placeholder"
  />
</template>
