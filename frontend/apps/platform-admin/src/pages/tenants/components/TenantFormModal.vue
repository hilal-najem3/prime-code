<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ t("tenants.create") }}
      </h2>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <FormField :error="getFirstError('name')">
          <TextInput
            v-model="form.name"
            :placeholder="t('tenants.fields.name')"
          />
        </FormField>

        <!-- Slug -->
        <FormField :error="getFirstError('slug')">
          <TextInput
            v-model="form.slug"
            :placeholder="t('tenants.fields.slug')"
          />
        </FormField>

        <!-- Username -->
        <FormField :error="getFirstError('db_username')">
          <TextInput
            v-model="form.db_username"
            :placeholder="t('tenants.fields.username')"
          />
        </FormField>

        <!-- Password -->
        <FormField :error="getFirstError('db_password')">
          <PasswordInput
            v-model="form.db_password"
            :placeholder="t('tenants.fields.password')"
            :translations="{
              show: t('common.show'),
              hide: t('common.hide'),
            }"
          />
        </FormField>

        <!-- Domain -->
        <FormField :error="getFirstError('domain')">
          <TextInput
            v-model="form.domain"
            :placeholder="t('tenants.fields.domain')"
          />
        </FormField>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            {{ t("tenants.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{ t("tenants.actions.create") }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { tenantService } from "@core/api/services/tenantService";
import { useAction } from "@core/composables/useAction";
import { Modal, Button, TextInput, FormField, PasswordInput } from "@ui";
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
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
  (e: "created"): void;
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
  db_username: "",
  db_password: "",
});

const errors = ref<Record<string, string[]>>({});
const { loading, execute } = useAction();
const { show } = useToast();

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
  form.db_username = "";
  form.db_password = "";
  errors.value = {};
};

const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      await tenantService.create(form);

      show(t("tenants.messages.created"), "success");

      emit("saved");
      emit("created");
      reset();
      close();
    } catch (e: any) {
      if (e.errors) {
        errors.value = e.errors;
      }
    }
  });
};
</script>
