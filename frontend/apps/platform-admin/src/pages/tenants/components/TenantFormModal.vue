<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? t("tenants.actions.update") : t("tenants.create") }}
      </h2>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <div>
          <TextInput
            v-model="form.name"
            :placeholder="t('tenants.fields.name')"
          />
          <p v-if="errors.name" class="text-sm text-state-danger mt-1">
            {{ errors.name[0] }}
          </p>
        </div>

        <!-- Slug -->
        <div>
          <TextInput
            v-model="form.slug"
            :placeholder="t('tenants.fields.slug')"
            :disabled="isEdit"
          />
          <p v-if="errors.slug" class="text-sm text-state-danger mt-1">
            {{ errors.slug[0] }}
          </p>
        </div>

        <!-- Domain -->
        <div>
          <TextInput
            v-model="form.domain"
            :placeholder="t('tenants.fields.domain')"
          />
          <p v-if="errors.domain" class="text-sm text-state-danger mt-1">
            {{ errors.domain[0] }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            {{ t("tenants.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{
              isEdit ? t("tenants.actions.update") : t("tenants.actions.create")
            }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { tenantService } from "@core/api/services/tenantService";
import { Modal, Button, TextInput } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

/*
|--------------------------------------------------------------------------
| Props / Emits
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  modelValue: boolean;
  tenant?: any | null; // ← NEW
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "created"): void;
  (e: "saved"): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const form = reactive({
  name: "",
  slug: "",
  domain: "",
});

const isEdit = computed(() => !!props.tenant);

const errors = ref<Record<string, string[]>>({});
const loading = ref(false);

const { show } = useToast();

/*
|--------------------------------------------------------------------------
| Watch Reset
|--------------------------------------------------------------------------
*/

watch(
  () => props.modelValue,
  (val) => {
    if (!val) return;

    if (props.tenant) {
      form.name = props.tenant.name;
      form.slug = props.tenant.slug;
      form.domain = props.tenant.domain;
    } else {
      reset();
    }
  },
  { immediate: true },
);

/*
|--------------------------------------------------------------------------
| Methods
|--------------------------------------------------------------------------
*/

const close = () => {
  emit("update:modelValue", false);
};

const reset = () => {
  form.name = "";
  form.slug = "";
  form.domain = "";
  errors.value = {};
};

const submit = async () => {
  loading.value = true;
  errors.value = {};

  try {
    if (isEdit.value && props.tenant) {
      await tenantService.update(props.tenant.id, {
        name: form.name,
        domain: form.domain,
      });
    } else {
      await tenantService.create(form);
    }

    show(
      t(isEdit.value ? "tenants.messages.updated" : "tenants.messages.created"),
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
