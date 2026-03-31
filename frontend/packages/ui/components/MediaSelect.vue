<script setup lang="ts">
import { SelectInput } from "@ui";
import { mediaApi } from "@core/api/services/media";

const props = defineProps<{
  modelValue: number | number[] | null;
  multiple?: boolean;
  collection?: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: any): void;
}>();

const fetchMedia = async (query: string) => {
  const data = await mediaApi.search(query);

  return data.map((m: any) => ({
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
    placeholder="Select media"
  />
</template>
