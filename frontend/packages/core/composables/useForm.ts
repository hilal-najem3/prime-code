import { reactive, ref } from "vue";

type Errors = Record<string, string | null>;

export function useForm<T extends Record<string, any>>(initialValues: T) {
  const form = reactive({ ...initialValues });
  const errors = reactive<Errors>({});
  const loading = ref(false);

  /*
  |--------------------------------------------------------------------------
  | Set Errors (from backend)
  |--------------------------------------------------------------------------
  */
  const setErrors = (backendErrors: Record<string, string[]>) => {
    Object.keys(backendErrors).forEach((key) => {
      errors[key] = backendErrors[key][0]; // first message only
    });
  };

  /*
  |--------------------------------------------------------------------------
  | Clear Errors
  |--------------------------------------------------------------------------
  */
  const clearError = (field: string) => {
    errors[field] = null;
  };

  const resetErrors = () => {
    Object.keys(errors).forEach((key) => (errors[key] = null));
  };

  /*
  |--------------------------------------------------------------------------
  | Submit Handler
  |--------------------------------------------------------------------------
  */
  const submit = async (callback: () => Promise<any>) => {
    loading.value = true;
    resetErrors();

    try {
      await callback();
    } catch (error: any) {
      if (error?.errors) {
        setErrors(error.errors);
      }
      throw error;
    } finally {
      loading.value = false;
    }
  };

  return {
    form,
    errors,
    loading,
    submit,
    clearError,
  };
}
