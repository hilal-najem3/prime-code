<script setup lang="ts">
import { computed, useAttrs } from "vue";
import { Button, Badge } from "@ui";
import { X } from "lucide-vue-next";

defineOptions({ inheritAttrs: false });

const props = defineProps<{
  modelValue: File[];
  accept?: string;
  multiple?: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: File[]): void;
}>();

const attrs = useAttrs();

const list = computed(() => props.modelValue || []);

function onNativeChange(e: Event) {
  const target = e.target as HTMLInputElement;
  const picked = target.files?.length ? Array.from(target.files) : [];
  if (!picked.length) {
    target.value = "";
    return;
  }
  emit("update:modelValue", [...list.value, ...picked]);
  target.value = "";
}

function removeAt(index: number) {
  const next = list.value.slice();
  next.splice(index, 1);
  emit("update:modelValue", next);
}
</script>

<template>
  <div class="space-y-2 w-full">
    <div class="flex flex-wrap gap-2 items-center">
      <label class="inline-flex cursor-pointer">
        <input
          v-bind="attrs"
          type="file"
          class="sr-only peer"
          :accept="accept"
          :multiple="multiple !== false"
          @change="onNativeChange"
        />
        <Button type="button" variant="secondary">
          Add files (private vault)
        </Button>
      </label>
      <span class="text-sm text-text-muted">
        Stored as sensitive documents on the tenant private disk.
      </span>
    </div>

    <div v-if="list.length" class="flex flex-col gap-1">
      <div
        v-for="(file, i) in list"
        :key="`${file.name}-${i}`"
        class="flex items-center justify-between rounded-lg border border-border px-3 py-2 gap-2"
      >
        <Badge variant="secondary">{{ file.name }}</Badge>
        <Button type="button" variant="outline" title="Remove" @click="removeAt(i)">
          <X class="size-4" />
        </Button>
      </div>
    </div>
  </div>
</template>
