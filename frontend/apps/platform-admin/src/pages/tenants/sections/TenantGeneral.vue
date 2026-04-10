<template>
  <Card class="space-y-6 max-w-2xl">
    <!-- Title -->
    <h2 class="text-lg font-semibold">
      {{ t("tenants.general") || "General Information" }}
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

      <!-- Domain -->
      <FormField :error="getFirstError('domain')">
        <TextInput
          v-model="form.domain"
          :placeholder="t('tenants.fields.domain')"
        />
      </FormField>

      <!-- Actions -->
      <div class="flex justify-end">
        <Button :loading="loading" type="submit">
          {{ t("common.save") || "Save Changes" }}
        </Button>
      </div>
    </form>
  </Card>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from "vue";
import { Card, Button, TextInput, FormField } from "@ui";
import { tenantService } from "@core/api/services/tenantService";
import { useAction } from "@core/composables/useAction";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/
const props = defineProps<{
  tenant: any;
}>();

/*
|--------------------------------------------------------------------------
| i18n / Toast
|--------------------------------------------------------------------------
*/
const { t } = useI18n();
const { show } = useToast();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/
const form = reactive({
  name: "",
  domain: "",
});

const errors = ref<Record<string, string[]>>({});

const { loading, execute } = useAction();

/*
|--------------------------------------------------------------------------
| Watch (sync tenant → form)
|--------------------------------------------------------------------------
*/
watch(
  () => props.tenant,
  (tenant) => {
    if (!tenant) return;

    form.name = tenant.name;
    form.domain = tenant.domain;
  },
  { immediate: true },
);

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
const getFirstError = (field: string) => errors.value[field]?.[0] || null;

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/
const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      await tenantService.update(props.tenant.id, form);

      show(t("tenants.messages.updated"), "success");
    } catch (e: any) {
      if (e.errors) {
        errors.value = e.errors;
      }
    }
  });
};
</script>
