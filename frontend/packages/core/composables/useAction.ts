import { ref } from "vue";

export function useAction() {
  const loading = ref(false);

  const execute = async (callback: () => Promise<void>) => {
    if (loading.value) return;

    loading.value = true;

    try {
      await callback();
    } finally {
      loading.value = false;
    }
  };

  return {
    loading,
    execute,
  };
}
