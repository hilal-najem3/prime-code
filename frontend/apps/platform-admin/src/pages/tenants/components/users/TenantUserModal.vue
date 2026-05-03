<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? "Update User" : "Create User" }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <FormField :error="getFirstError('name')">
          <TextInput v-model="form.name" placeholder="Name" />
        </FormField>

        <FormField :error="getFirstError('email')">
          <TextInput v-model="form.email" placeholder="Email" />
        </FormField>

        <FormField :error="getFirstError('password')">
          <PasswordInput
            v-model="form.password"
            :placeholder="isEdit ? 'Password (optional)' : 'Password'"
          />
        </FormField>

        <FormField :error="getFirstError('roles')">
          <SelectInput
            v-model="form.roles"
            :options="rolesOptions"
            placeholder="Roles"
            multiple
          />
        </FormField>

        <label class="flex items-center gap-2 text-sm text-text-primary">
          <input type="checkbox" v-model="form.enabled" />
          Enabled
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
import { tenantUsersService } from "@core/api/services/tenants/usersService";
import { tenantRolesService } from "@core/api/services/tenants/rolesService";
import { useAction } from "@core/composables/useAction";
import {
  Modal,
  Button,
  TextInput,
  FormField,
  PasswordInput,
  SelectInput,
  useToast,
} from "@ui";

const props = defineProps<{
  modelValue: boolean;
  tenantId?: number | string | null;
  user?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const form = reactive({
  name: "",
  email: "",
  password: "",
  enabled: true,
  roles: [] as number[],
});

const rolesOptions = ref<{ label: string; value: number }[]>([]);
const errors = ref<Record<string, string[]>>({});
const isEdit = computed(() => !!props.user);

const { loading, execute } = useAction();
const { show } = useToast();

const loadRoles = async () => {
  if (!props.tenantId) {
    rolesOptions.value = [];
    return;
  }

  const res = await tenantRolesService.getAll(props.tenantId);

  rolesOptions.value = (Array.isArray(res.data) ? res.data : []).map((r: any) => ({
    label: r.name,
    value: r.id,
  }));
};

watch(
  () => props.modelValue,
  async (open) => {
    if (!open) return;

    errors.value = {};
    await loadRoles();

    if (props.user) {
      form.name = props.user.name ?? "";
      form.email = props.user.email ?? "";
      form.password = "";
      form.enabled = Boolean(props.user.enabled);
      form.roles = (props.user.roles || []).map((r: any) => r.id);
      return;
    }

    form.name = "";
    form.email = "";
    form.password = "";
    form.enabled = true;
    form.roles = [];
  },
  { immediate: true },
);

const close = () => emit("update:modelValue", false);
const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  const tenantId = props.tenantId;

  if (!tenantId) return;

  errors.value = {};

  await execute(async () => {
    try {
      if (isEdit.value && props.user) {
        await tenantUsersService.update(tenantId, props.user.id, {
          name: form.name,
          email: form.email,
          password: form.password || undefined,
          enabled: form.enabled,
          roles: form.roles,
        });

        show("User updated successfully", "success");
      } else {
        await tenantUsersService.create(tenantId, {
          name: form.name,
          email: form.email,
          password: form.password,
          enabled: form.enabled,
          roles: form.roles,
        });

        show("User created successfully", "success");
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
