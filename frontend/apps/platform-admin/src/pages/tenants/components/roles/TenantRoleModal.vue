<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? "Update Role" : "Create Role" }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <FormField :error="getFirstError('name')">
          <TextInput v-model="form.name" placeholder="Role name" />
        </FormField>

        <FormField :error="getFirstError('slug')">
          <TextInput
            v-model="form.slug"
            placeholder="Role slug"
            :disabled="isEdit"
          />
        </FormField>

        <div>
          <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium text-text-primary">Permissions</p>

            <Button variant="secondary" type="button" @click="clearPermissions">
              Uncheck All
            </Button>
          </div>

          <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-4">
            <div
              v-for="(group, module) in groupedPermissions"
              :key="module"
              class="space-y-2"
            >
              <p class="text-xs font-semibold text-text-secondary uppercase">
                {{ module }}
              </p>

              <div class="grid grid-cols-2 gap-2 text-text-primary">
                <label
                  v-for="perm in group"
                  :key="perm.id"
                  class="flex items-center gap-2 text-sm cursor-pointer"
                >
                  <input
                    type="checkbox"
                    :value="perm.id"
                    v-model="form.permissions"
                  />
                  <span>{{ perm.slug }}</span>
                </label>
              </div>
            </div>
          </div>
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
import { tenantRolesService } from "@core/api/services/tenants/rolesService";
import { permissionsService } from "@core/api/services/permissionsService";
import { useAction } from "@core/composables/useAction";
import { Modal, Button, TextInput, FormField, useToast } from "@ui";

const props = defineProps<{
  modelValue: boolean;
  tenantId?: number | string | null;
  role?: any | null;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const form = reactive({
  name: "",
  slug: "",
  permissions: [] as number[],
});

const permissions = ref<any[]>([]);
const errors = ref<Record<string, string[]>>({});
const isEdit = computed(() => !!props.role);
const { loading, execute } = useAction();
const { show } = useToast();

const groupedPermissions = computed(() => {
  const groups: Record<string, any[]> = {};

  permissions.value.forEach((permission) => {
    if (!groups[permission.module]) {
      groups[permission.module] = [];
    }

    groups[permission.module].push(permission);
  });

  return groups;
});

const loadPermissions = async () => {
  const res = await permissionsService.getAll();
  permissions.value = Array.isArray(res.data) ? res.data : [];
};

watch(
  () => props.modelValue,
  async (open) => {
    if (!open) return;

    errors.value = {};
    await loadPermissions();

    if (props.role) {
      form.name = props.role.name ?? "";
      form.slug = props.role.slug ?? "";
      form.permissions = (props.role.permissions || []).map((p: any) => p.id);
      return;
    }

    form.name = "";
    form.slug = "";
    form.permissions = [];
  },
  { immediate: true },
);

const close = () => emit("update:modelValue", false);
const getFirstError = (field: string) => errors.value[field]?.[0] || null;
const clearPermissions = () => {
  form.permissions = [];
};

const submit = async () => {
  if (!props.tenantId) return;

  errors.value = {};

  await execute(async () => {
    try {
      const payload = {
        name: form.name,
        slug: form.slug,
        permissions: form.permissions,
      };

      if (isEdit.value && props.role) {
        await tenantRolesService.update(
          props.tenantId as string | number,
          props.role.id,
          payload,
        );
        show("Role updated successfully", "success");
      } else {
        await tenantRolesService.create(
          props.tenantId as string | number,
          payload,
        );
        show("Role created successfully", "success");
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
