<script setup lang="ts">
// path to: frontend/apps/tenant-admin/src/pages/patients/components/PatientAddressesEditor.vue
import { computed } from "vue";
import { Button, FormField, TextInput } from "@ui";
import { Trash2 } from "lucide-vue-next";
import type { PatientAddress } from "@core/api/services/patientService";

defineOptions({ inheritAttrs: false });

const props = defineProps<{
  modelValue: PatientAddress[];
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: PatientAddress[]): void;
}>();

const rows = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

function blankRow(): PatientAddress {
  return {
    country: "",
    city: "",
    street: "",
    building: "",
    floor: "",
    notes: "",
  };
}

function addRow() {
  rows.value = [...(rows.value || []), blankRow()];
}

function removeRow(index: number) {
  const next = (rows.value || []).slice();
  next.splice(index, 1);
  rows.value = next.length ? next : [blankRow()];
}

function patchRow(index: number, patch: Partial<PatientAddress>) {
  rows.value = (rows.value || []).map((row, i) =>
    i === index ? { ...row, ...patch } : row,
  );
}
</script>

<template>
  <div class="space-y-3">
    <div class="flex justify-between items-center">
      <h3 class="text-sm font-semibold text-text-primary">Addresses</h3>
      <Button type="button" variant="secondary" @click="addRow">
        Add address
      </Button>
    </div>

    <div
      v-if="rows.length === 0"
      class="text-sm text-text-muted border border-border border-dashed rounded-lg px-4 py-3"
    >
      No addresses. Add at least one address row.
    </div>

    <div
      v-for="(row, idx) in rows"
      :key="`adr-${idx}`"
      class="rounded-xl border border-border p-4 space-y-4"
    >
      <div class="flex justify-end">
        <Button
          type="button"
          variant="outline"
          title="Remove address"
          @click="removeRow(idx)"
        >
          <Trash2 class="size-4" />
        </Button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Country
          </p>
          <TextInput
            :model-value="row.country ?? ''"
            @update:modelValue="(v) => patchRow(idx, { country: v })"
          />
        </FormField>
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">City</p>
          <TextInput
            :model-value="row.city ?? ''"
            @update:modelValue="(v) => patchRow(idx, { city: v })"
          />
        </FormField>
      </div>

      <FormField>
        <p class="text-xs font-medium text-text-muted uppercase mb-1">Street</p>
        <TextInput
          :model-value="row.street ?? ''"
          @update:modelValue="(v) => patchRow(idx, { street: v })"
        />
      </FormField>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Building
          </p>
          <TextInput
            :model-value="row.building ?? ''"
            @update:modelValue="(v) => patchRow(idx, { building: v })"
          />
        </FormField>
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Floor
          </p>
          <TextInput
            :model-value="row.floor ?? ''"
            @update:modelValue="(v) => patchRow(idx, { floor: v })"
          />
        </FormField>
      </div>

      <FormField>
        <p class="text-xs font-medium text-text-muted uppercase mb-1">
          Address notes
        </p>
        <TextInput
          :model-value="row.notes ?? ''"
          @update:modelValue="(v) => patchRow(idx, { notes: v })"
        />
      </FormField>
    </div>
  </div>
</template>
