<template>
  <Modal :modelValue="modelValue" @update:modelValue="close">
    <div class="space-y-6">
      <h2 class="text-lg font-semibold text-center">Change Password</h2>

      <form @submit.prevent="submit" class="space-y-4">
        <FormField :error="getFirstError('current_password')">
          <PasswordInput
            v-model="form.current_password"
            placeholder="Current Password"
          />
        </FormField>

        <FormField :error="getFirstError('password')">
          <PasswordInput v-model="form.password" placeholder="New Password" />
        </FormField>

        <FormField :error="getFirstError('password_confirmation')">
          <PasswordInput
            v-model="form.password_confirmation"
            placeholder="Confirm Password"
          />
        </FormField>

        <div class="flex justify-end gap-2 pt-4">
          <Button variant="secondary" @click="close"> Cancel </Button>

          <Button :loading="loading" type="submit"> Update </Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { profileService } from "@core/api/services/profileService";
import { useAction } from "@core/composables/useAction";
import { useToast } from "@ui";
import { Modal, Button, PasswordInput, FormField } from "@ui";

const props = defineProps<{ modelValue: boolean }>();

const emit = defineEmits(["update:modelValue"]);

const { loading, execute } = useAction();
const { show } = useToast();

const form = reactive({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const errors = ref<Record<string, string[]>>({});

const close = () => emit("update:modelValue", false);

const getFirstError = (f: string) => errors.value[f]?.[0];

const submit = async () => {
  errors.value = {};

  await execute(async () => {
    try {
      await profileService.updatePassword(form);

      show("Password updated. Please login again.", "success");

      close();

      // 🔥 force logout
      window.location.href = "/login";
    } catch (e: any) {
      if (e.errors) errors.value = e.errors;
    }
  });
};
</script>
