<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <!-- Title -->
      <h2 class="text-lg font-semibold text-text-primary text-center">
        {{ isEdit ? t("plans.actions.update") : t("plans.actions.create") }}
      </h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <FormField :error="getFirstError('name')">
          <TextInput v-model="form.name" :placeholder="t('plans.fields.name')" />
        </FormField>

        <!-- Slug -->
        <FormField :error="getFirstError('slug')">
          <TextInput
            v-model="form.slug"
            :placeholder="t('plans.fields.slug')"
            :disabled="isEdit"
          />
        </FormField>

        <!-- Price -->
        <FormField :error="getFirstError('price')">
          <TextInput
            v-model="form.price"
            type="number"
            :placeholder="t('plans.fields.price')"
          />
        </FormField>

        <!-- Modules -->
        <div class="space-y-2">
          <p class="text-sm text-text-secondary">
            {{ t("plans.fields.modules") }}
          </p>

          <div class="grid grid-cols-2 gap-2">
            <label
              v-for="module in modules"
              :key="module.id"
              class="flex items-center gap-2"
            >
              <input
                type="checkbox"
                :value="module.id"
                v-model="form.modules"
              />
              {{ module.name }}
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" @click="close" type="button">
            {{ t("plans.actions.cancel") }}
          </Button>

          <Button :loading="loading" type="submit">
            {{ isEdit ? t("plans.actions.update") : t("plans.actions.create") }}
          </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { reactive, ref, watch, computed, onMounted } from "vue";
import { planService } from "@core/api/services/planService";
import { moduleService } from "@core/api/services/moduleService";
import { useAction } from "@core/composables/useAction";
import { Modal, Button, TextInput, FormField } from "@ui";
import { useToast } from "@ui";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const { show } = useToast();

const props = defineProps<{
  modelValue: boolean;
  plan?: any | null;
}>();

const emit = defineEmits(["update:modelValue", "saved"]);

const form = reactive({
  name: "",
  slug: "",
  price: "0",
  modules: [] as number[],
});

const modules = ref<any[]>([]);
const errors = ref<Record<string, string[]>>({});
const { loading, execute } = useAction();

const isEdit = computed(() => !!props.plan);

const loadModules = async () => {
  const res = await moduleService.getAll();
  modules.value = Array.isArray(res.data) ? res.data : [];
};

onMounted(loadModules);

watch(
  () => props.modelValue,
  (val) => {
    if (!val) return;

    if (props.plan) {
      form.name = props.plan.name;
      form.slug = props.plan.slug;
      form.price = String(props.plan.price);
      form.modules = props.plan.modules?.map((m: any) => m.id) || [];
    } else {
      form.name = "";
      form.slug = "";
      form.price = "0";
      form.modules = [];
    }
  },
  { immediate: true },
);

const close = () => emit("update:modelValue", false);
const getFirstError = (field: string) => errors.value[field]?.[0] || null;

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      const formData = {
        ...form,
        price: Number(form.price),
      };

      if (isEdit.value && props.plan) {
        await planService.update(props.plan.id, formData);
      } else {
        await planService.create(formData);
      }

      show(
        t(isEdit.value ? "plans.messages.updated" : "plans.messages.created"),
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
