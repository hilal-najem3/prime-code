<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? t("modules.actions.update") : t("modules.actions.create") }}
      </h2>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <div>
          <TextInput
            v-model="form.name"
            :placeholder="t('modules.fields.name')"
          />
          <p v-if="errors.name" class="text-sm text-state-danger mt-1">
            {{ errors.name[0] }}
          </p>
        </div>

        <!-- Slug -->
        <div>
          <TextInput
            v-model="form.slug"
            :placeholder="t('modules.fields.slug')"
            :disabled="isEdit"
          />
          <p v-if="errors.slug" class="text-sm text-state-danger mt-1">
            {{ errors.slug[0] }}
          </p>
        </div>

        <!-- Enabled -->
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="form.enabled" />
          <span>{{ t("modules.fields.enabled") }}</span>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            {{ t("modules.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{
              isEdit ? t("modules.actions.update") : t("modules.actions.create")
            }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { moduleService } from "@core/api/services/moduleService";
import { Modal, Button, TextInput } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps<{
  modelValue: boolean;
  module?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const form = reactive({
  name: "",
  slug: "",
  enabled: true,
});

const isEdit = computed(() => !!props.module);

const errors = ref<Record<string, string[]>>({});
const loading = ref(false);

const { show } = useToast();

watch(
  () => props.modelValue,
  (val) => {
    if (!val) return;

    if (props.module) {
      form.name = props.module.name;
      form.slug = props.module.slug;
      form.enabled = props.module.enabled;
    } else {
      reset();
    }
  },
  { immediate: true },
);

const close = () => emit("update:modelValue", false);

const reset = () => {
  form.name = "";
  form.slug = "";
  form.enabled = true;
  errors.value = {};
};

const submit = async () => {
  loading.value = true;
  errors.value = {};

  try {
    if (isEdit.value && props.module) {
      await moduleService.update(props.module.id, form);
    } else {
      await moduleService.create(form);
    }

    show(
      t(isEdit.value ? "modules.messages.updated" : "modules.messages.created"),
      "success",
    );

    emit("saved");
    close();
  } catch (e: any) {
    if (e.errors) {
      errors.value = e.errors;
    }
  } finally {
    loading.value = false;
  }
};
</script>
