<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? "Update Page" : "Create Page" }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <FormField :error="getFirstError('title.en') || getFirstError('title')">
          <TextInput v-model="form.title_en" placeholder="Title (EN)" />
        </FormField>

        <FormField :error="getFirstError('slug.en') || getFirstError('slug')">
          <TextInput v-model="form.slug_en" placeholder="Slug (EN)" />
        </FormField>

        <FormField :error="getFirstError('layout')">
          <TextInput v-model="form.layout" placeholder="Layout (optional)" />
        </FormField>

        <FormField :error="getFirstError('status')">
          <select
            v-model="form.status"
            class="w-full rounded-lg border border-border bg-bg-primary px-4 py-2 text-text-primary"
          >
            <option value="draft">Draft</option>
            <option value="published">Published</option>
          </select>
        </FormField>

        <FormField :error="getFirstError('meta_title.en') || getFirstError('meta_title')">
          <TextInput v-model="form.meta_title_en" placeholder="Meta title (EN, optional)" />
        </FormField>

        <FormField
          :error="getFirstError('meta_description.en') || getFirstError('meta_description')"
        >
          <textarea
            v-model="form.meta_description_en"
            class="w-full rounded-lg border border-border bg-bg-primary px-4 py-2 text-text-primary"
            rows="3"
            placeholder="Meta description (EN, optional)"
          />
        </FormField>

        <label class="flex items-center gap-2 text-sm text-text-primary">
          <input v-model="form.is_homepage" type="checkbox" />
          Set as homepage
        </label>

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
import { pageService } from "@core/api/services/tenants/pageService";
import { useAction } from "@core/composables/useAction";
import { useToast } from "@ui";
import { Button, FormField, Modal, TextInput } from "@ui";

const props = defineProps<{
  modelValue: boolean;
  tenantId?: number | string | null;
  page?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const { show } = useToast();
const { loading, execute } = useAction();

const errors = ref<Record<string, string[]>>({});

const form = reactive({
  title_en: "",
  slug_en: "",
  layout: "",
  status: "draft",
  is_homepage: false,
  meta_title_en: "",
  meta_description_en: "",
});

const isEdit = computed(() => !!props.page);

watch(
  () => props.modelValue,
  (open) => {
    if (!open) return;

    errors.value = {};

    if (props.page) {
      form.title_en = props.page?.title?.en ?? "";
      form.slug_en = props.page?.slug?.en ?? "";
      form.layout = props.page?.layout ?? "";
      form.status = props.page?.status ?? "draft";
      form.is_homepage = Boolean(props.page?.is_homepage);
      form.meta_title_en = props.page?.meta_title?.en ?? "";
      form.meta_description_en = props.page?.meta_description?.en ?? "";
      return;
    }

    form.title_en = "";
    form.slug_en = "";
    form.layout = "";
    form.status = "draft";
    form.is_homepage = false;
    form.meta_title_en = "";
    form.meta_description_en = "";
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
        title: {
          en: form.title_en,
        },
        slug: {
          en: form.slug_en,
        },
        content: {},
        layout: form.layout || null,
        status: form.status,
        is_homepage: form.is_homepage,
        meta_title: form.meta_title_en
          ? {
              en: form.meta_title_en,
            }
          : null,
        meta_description: form.meta_description_en
          ? {
              en: form.meta_description_en,
            }
          : null,
      };

      if (isEdit.value && props.page) {
        await pageService.update(props.tenantId, props.page.id, payload);
        show("Page updated successfully", "success");
      } else {
        await pageService.create(props.tenantId, payload);
        show("Page created successfully", "success");
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
