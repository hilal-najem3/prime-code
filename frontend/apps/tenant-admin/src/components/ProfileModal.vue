<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-center">Profile</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <FormField :error="getFirstError('name')">
          <TextInput v-model="form.name" placeholder="Name" />
        </FormField>

        <!-- Email -->
        <FormField :error="getFirstError('email')">
          <TextInput v-model="form.email" placeholder="Email" />
        </FormField>

        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" @click="close"> Cancel </Button>

          <Button :loading="loading" type="submit"> Save </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { reactive, watch, ref } from "vue";
import { profileService } from "@core/api/services/profileService";
import { useAuthStore } from "@/core/store/authStore";
import { useAction } from "@core/composables/useAction";
import { useToast } from "@ui";
import { Modal, Button, TextInput, FormField } from "@ui";

const props = defineProps<{ modelValue: boolean }>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
}>();

const auth = useAuthStore();
const { loading, execute } = useAction();
const { show } = useToast();

const form = reactive({
  name: "",
  email: "",
});

const errors = ref<Record<string, string[]>>({});

watch(
  () => props.modelValue,
  (val) => {
    if (!val) return;

    form.name = auth.userName || "";
    form.email = auth.userEmail || "";
  },
);

const close = () => emit("update:modelValue", false);

const getFirstError = (f: string) => errors.value[f]?.[0];

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      const res = await profileService.update(form);

      show("Profile updated", "success");

      close();

      window.location.reload();
    } catch (e: any) {
      if (e.errors) errors.value = e.errors;
    }
  });
};
</script>
