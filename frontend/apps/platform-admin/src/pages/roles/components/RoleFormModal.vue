<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? t("roles.actions.update") : t("roles.actions.create") }}
      </h2>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <FormField :error="getFirstError('name')">
          <TextInput
            v-model="form.name"
            :placeholder="t('roles.fields.name')"
          />
        </FormField>

        <!-- Slug -->
        <FormField :error="getFirstError('slug')">
          <TextInput
            v-model="form.slug"
            :placeholder="t('roles.fields.slug')"
            :disabled="isEdit"
          />
        </FormField>

        <!-- Permissions -->
        <div>
          <p class="text-sm font-medium text-text-primary mb-2">
            {{ t("roles.fields.permissions") }}
          </p>

          <div class="max-h-64 overflow-y-auto border rounded-lg p-3 space-y-4">
            <div
              v-for="(group, module) in groupedPermissions"
              :key="module"
              class="space-y-2"
            >
              <!-- Module Title -->
              <p class="text-xs font-semibold text-text-secondary uppercase">
                {{ module }}
              </p>

              <!-- Permissions -->
              <div class="grid grid-cols-2 gap-2">
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

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" type="button" @click="close">
            {{ t("roles.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{ isEdit ? t("roles.actions.update") : t("roles.actions.create") }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { computed, reactive, ref, watch } from "vue";
import { rolesService } from "@core/api/services/rolesService";
import { permissionsService } from "@core/api/services/permissionsService";
import { useAction } from "@core/composables/useAction";
import { Modal, Button, TextInput, FormField } from "@ui";
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
  role?: any | null;
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
  slug: "",
  permissions: [] as number[],
});

const permissions = ref<any[]>([]);
const errors = ref<Record<string, string[]>>({});

const isEdit = computed(() => !!props.role);
const { loading, execute } = useAction();
const { show } = useToast();

/*
|--------------------------------------------------------------------------
| Group Permissions
|--------------------------------------------------------------------------
*/
const groupedPermissions = computed(() => {
  const groups: Record<string, any[]> = {};

  permissions.value.forEach((p) => {
    if (!groups[p.module]) {
      groups[p.module] = [];
    }
    groups[p.module].push(p);
  });

  return groups;
});

/*
|--------------------------------------------------------------------------
| Load Permissions
|--------------------------------------------------------------------------
*/
const loadPermissions = async () => {
  const res = await permissionsService.getAll();
  permissions.value = Array.isArray(res.data) ? res.data : [];
};

/*
|--------------------------------------------------------------------------
| Watch Open
|--------------------------------------------------------------------------
*/
watch(
  () => props.modelValue,
  async (val) => {
    if (!val) return;

    await loadPermissions();

    if (props.role) {
      form.name = props.role.name;
      form.slug = props.role.slug;

      // 🔥 IMPORTANT MAPPING
      form.permissions = (props.role.permissions || []).map((p: any) => p.id);
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
  form.permissions = [];
  errors.value = {};
};

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
      if (isEdit.value && props.role) {
        await rolesService.update(props.role.id, form);
      } else {
        await rolesService.create(form);
      }

      show(
        t(isEdit.value ? "roles.messages.updated" : "roles.messages.created"),
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
