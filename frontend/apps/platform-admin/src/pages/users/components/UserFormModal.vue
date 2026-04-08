<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? t("users.actions.update") : t("users.actions.create") }}
      </h2>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <FormField :error="getFirstError('name')">
          <TextInput
            v-model="form.name"
            :placeholder="t('users.fields.name') || 'Name'"
          />
        </FormField>

        <!-- Email -->
        <FormField :error="getFirstError('email')">
          <TextInput
            v-model="form.email"
            :placeholder="t('users.fields.email') || 'Email'"
          />
        </FormField>

        <!-- Password -->
        <FormField :error="getFirstError('password')">
          <PasswordInput
            v-model="form.password"
            :placeholder="t('users.fields.password') || 'Password'"
          />
        </FormField>

        <!-- Roles -->
        <FormField :error="getFirstError('roles')">
          <SelectInput
            v-model="form.roles"
            :options="rolesOptions"
            :placeholder="t('users.fields.roles') || 'Roles'"
            multiple
          />
        </FormField>

        <!-- Enabled -->
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="form.enabled" />
          <span>{{ t("users.fields.enabled") || "Enabled" }}</span>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            {{ t("users.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{ isEdit ? t("users.actions.update") : t("users.actions.create") }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { usersService } from "@core/api/services/usersService";
import { useAction } from "@core/composables/useAction";
import {
  Modal,
  Button,
  TextInput,
  FormField,
  PasswordInput,
  SelectInput,
} from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";
import http from "@core/api/http";

const { t } = useI18n();

/*
|--------------------------------------------------------------------------
| Props / Emits
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  modelValue: boolean;
  user?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const form = reactive({
  name: "",
  email: "",
  password: "",
  enabled: true,
  roles: [] as number[],
});

const rolesOptions = ref<{ label: string; value: number }[]>([]);

const isEdit = computed(() => !!props.user);

const errors = ref<Record<string, string[]>>({});
const { loading, execute } = useAction();
const { show } = useToast();

/*
|--------------------------------------------------------------------------
| Load Roles
|--------------------------------------------------------------------------
*/

const loadRoles = async () => {
  const res = await http.get("/platform/roles");

  rolesOptions.value = (res.data || []).map((r: any) => ({
    label: r.name,
    value: r.id,
  }));
};

/*
|--------------------------------------------------------------------------
| Watch (same pattern as Tenant)
|--------------------------------------------------------------------------
*/

watch(
  () => props.modelValue,
  async (val) => {
    if (!val) return;

    await loadRoles();

    if (props.user) {
      form.name = props.user.name;
      form.email = props.user.email;
      form.password = "";
      form.enabled = props.user.enabled;

      form.roles = props.user.roles?.map((r: any) => r.id) || [];
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
  form.email = "";
  form.password = "";
  form.enabled = true;
  form.roles = [];
  errors.value = {};
};

const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      if (isEdit.value && props.user) {
        await usersService.update(props.user.id, {
          name: form.name,
          email: form.email,
          password: form.password || undefined,
          enabled: form.enabled,
          roles: form.roles,
        });
      } else {
        await usersService.create(form);
      }

      show(
        t(isEdit.value ? "users.messages.updated" : "users.messages.created"),
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
