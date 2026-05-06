<script setup lang="ts">
import { computed } from "vue";
import {
  Badge,
  Button,
  SelectInput,
  TextInput,
  FormField,
  DatePicker,
} from "@ui";
import { Trash2 } from "lucide-vue-next";
import type {
  PatientIdentityDraft,
  PatientIdentityType,
} from "@core/api/services/patientService";
import { MediaField } from "@media";

defineOptions({ inheritAttrs: false });

const props = defineProps<{
  modelValue: PatientIdentityDraft[];
  errorsPrefix?: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: PatientIdentityDraft[]): void;
  (e: "removed-identity", id: number): void;
}>();

const typeOptions = [
  { label: "ID Card", value: "id_card" },
  { label: "Passport", value: "passport" },
  { label: "Driver licence", value: "driver_license" },
];

const rows = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

function blankRow(): PatientIdentityDraft {
  return {
    type: "passport",
    number: "",
    issued_at: null,
    expires_at: null,
    notes: "",
    media: [], // ✅ FIXED
  };
}

function addRow() {
  rows.value = [...rows.value, blankRow()];
}

function removeRow(index: number) {
  const row = rows.value[index];
  if (row?.id != null) {
    emit("removed-identity", row.id);
  }
  const next = rows.value.slice();
  next.splice(index, 1);
  rows.value = next;
}

function patchRow(index: number, patch: Partial<PatientIdentityDraft>) {
  const next = rows.value.map((row, i) =>
    i === index ? { ...row, ...patch } : row,
  );
  rows.value = next;
}
</script>

<template>
  <div class="space-y-3">
    <div class="flex justify-between items-center">
      <h3 class="text-sm font-semibold text-text-primary">
        Identity documents
      </h3>
      <Button type="button" variant="secondary" @click="addRow">
        Add identity
      </Button>
    </div>

    <div
      v-if="rows.length === 0"
      class="text-sm text-text-muted border border-border border-dashed rounded-lg px-4 py-3"
    >
      No identity records.
    </div>

    <div
      v-for="(row, idx) in rows"
      :key="`${row.type}-${idx}-${row.id ?? 'n'}`"
      class="rounded-xl border border-border p-4 space-y-4"
    >
      <div class="flex justify-between items-start gap-3">
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Document type
          </p>
          <SelectInput
            :modelValue="row.type"
            :options="typeOptions"
            @update:modelValue="
              (v) =>
                patchRow(idx, {
                  type: String(v ?? 'passport') as PatientIdentityType,
                })
            "
          />
        </FormField>

        <Button type="button" variant="outline" @click="removeRow(idx)">
          <Trash2 class="size-4" />
        </Button>
      </div>

      <FormField>
        <p class="text-xs font-medium text-text-muted uppercase mb-1">Number</p>
        <TextInput
          :modelValue="row.number"
          @update:modelValue="(v) => patchRow(idx, { number: v })"
        />
      </FormField>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Issued
          </p>
          <DatePicker
            :modelValue="row.issued_at"
            @update:modelValue="
              (v) => patchRow(idx, { issued_at: v ? String(v) : null })
            "
          />
        </FormField>

        <FormField>
          <p class="text-xs font-medium text-text-muted uppercase mb-1">
            Expires
          </p>
          <DatePicker
            :modelValue="row.expires_at"
            @update:modelValue="
              (v) => patchRow(idx, { expires_at: v ? String(v) : null })
            "
          />
        </FormField>
      </div>

      <FormField>
        <p class="text-xs font-medium text-text-muted uppercase mb-1">Notes</p>
        <TextInput
          :modelValue="row.notes"
          @update:modelValue="(v) => patchRow(idx, { notes: v })"
        />
      </FormField>

      <!-- ✅ MEDIA FIELD -->
      <FormField>
        <p class="text-xs font-medium text-text-muted uppercase mb-1">
          Identity files (private)
        </p>

        <MediaField
          :modelValue="row.media"
          @update:modelValue="(val) => patchRow(idx, { media: val })"
          :multiple="true"
          collection="identity"
          directory="patients/identities"
          disk="private"
          mode="select"
        />
      </FormField>
    </div>
  </div>
</template>
