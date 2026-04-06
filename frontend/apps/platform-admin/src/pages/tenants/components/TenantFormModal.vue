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
            :disabled="isEdit"
          />
        </FormField>

        <!-- Domain -->
        <FormField :error="getFirstError('domain')">
          <TextInput
            v-model="form.domain"
            :placeholder="t('tenants.fields.domain')"
          />
        </FormField>

        <TenantSubscriptionCard
          v-if="isEdit && fullTenant"
          :tenant="fullTenant"
          @updated="onSubscriptionUpdated"
        />

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
import { useAction } from "@core/composables/useAction";
import { Modal, Button, TextInput, FormField } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";
import TenantSubscriptionCard from "./TenantSubscriptionCard.vue";

const { t } = useI18n();

/*
|--------------------------------------------------------------------------
| Props / Emits
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  modelValue: boolean;
  tenant?: any | null; // ? NEW
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
const fullTenant = ref<any>(null);
const { loading, execute } = useAction();

const { show } = useToast();

const onSubscriptionUpdated = async () => {
  if (!fullTenant.value?.id) return;

  const res = await tenantService.get(fullTenant.value.id);
  fullTenant.value = res.data;
  emit("saved");
};

/*
|--------------------------------------------------------------------------
| Watch Reset
|--------------------------------------------------------------------------
*/

watch(
  () => props.modelValue,
  async (val) => {
    if (!val) return;

    if (props.tenant) {
      fullTenant.value = { ...props.tenant };

      if (isEdit.value) {
        // Load full tenant data including subscription
        const res = await tenantService.get(props.tenant.id);
        fullTenant.value = res.data;
      }

      form.name = fullTenant.value.name;
      form.slug = fullTenant.value.slug;
      form.domain = fullTenant.value.domain;

      // await loadModules();
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

const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      if (isEdit.value && fullTenant.value) {
        await tenantService.update(fullTenant.value.id, {
          name: form.name,
          domain: form.domain,
        });

        // ?? Sync modules AFTER update
        // await tenantModuleService.sync(props.tenant.id, selectedModules.value);
      } else {
        await tenantService.create(form);
      }

      show(
        t(
          isEdit.value ? "tenants.messages.updated" : "tenants.messages.created",
        ),
        "success",
      );

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
