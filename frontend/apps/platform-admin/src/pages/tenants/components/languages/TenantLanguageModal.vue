<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? "Update Language" : "Create Language" }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <FormField :error="getFirstError('name')">
          <TextInput v-model="form.name" placeholder="Language Name" />
        </FormField>

        <FormField :error="getFirstError('slug')">
          <TextInput v-model="form.slug" placeholder="Slug (e.g. en, ar)" />
        </FormField>

        <FormField :error="getFirstError('direction')">
          <select
            v-model="form.direction"
            class="w-full rounded-lg border border-border bg-bg-primary px-4 py-2 text-text-primary"
          >
            <option value="ltr">LTR</option>
            <option value="rtl">RTL</option>
          </select>
        </FormField>

        <div class="space-y-3">
          <label class="flex items-center gap-2 text-sm text-text-primary">
            <input v-model="form.is_default" type="checkbox" />
            Set as default language
          </label>

          <label class="flex items-center gap-2 text-sm text-text-primary">
            <input v-model="form.is_active" type="checkbox" />
            Active
          </label>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            Cancel
          </Button>

          <Button :loading="loading" type="submit">
            {{ isEdit ? "Update" : "Create" }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { languageService } from "@core/api/services/tenants/languageService";
import { useAction } from "@core/composables/useAction";
import { useToast } from "@ui";
import { Button, FormField, Modal, TextInput } from "@ui";

const props = defineProps<{
  modelValue: boolean;
  tenantId?: number | string | null;
  language?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const { show } = useToast();
const { loading, execute } = useAction();

const errors = ref<Record<string, string[]>>({});

const form = reactive({
  name: "",
  slug: "",
  direction: "ltr",
  is_default: false,
  is_active: true,
});

const isEdit = computed(() => !!props.language);

watch(
  () => props.modelValue,
  (open) => {
    if (!open) return;

    errors.value = {};

    if (props.language) {
      form.name = props.language.name ?? "";
      form.slug = props.language.slug ?? "";
      form.direction = props.language.direction ?? "ltr";
      form.is_default = Boolean(props.language.is_default);
      form.is_active = Boolean(props.language.is_active);
      return;
    }

    form.name = "";
    form.slug = "";
    form.direction = "ltr";
    form.is_default = false;
    form.is_active = true;
  },
  { immediate: true },
);

const close = () => emit("update:modelValue", false);

const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  if (!props.tenantId) return;

  errors.value = {};

  await execute(async () => {
    try {
      const payload = {
        name: form.name,
        slug: form.slug,
        direction: form.direction,
        is_default: form.is_default,
        is_active: form.is_active,
      };

      if (isEdit.value && props.language) {
        await languageService.update(props.tenantId, props.language.id, payload);
        show("Language updated successfully", "success");
      } else {
        await languageService.create(props.tenantId, payload);
        show("Language created successfully", "success");
      }

      emit("saved");
      close();
    } catch (e: any) {
      if (e.errors) {
        errors.value = e.errors;
      }
    }
  });
};
</script>
