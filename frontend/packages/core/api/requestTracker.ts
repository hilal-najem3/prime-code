import { ref, computed } from "vue";

/*
|--------------------------------------------------------------------------
| Global State (IMPORTANT)
|--------------------------------------------------------------------------
*/

const requests = ref(0);

const isLoading = computed(() => requests.value > 0);

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

export const useRequestTracker = () => {
  const start = () => {
    requests.value++;
  };

  const end = () => {
    requests.value = Math.max(0, requests.value - 1);
  };

  return {
    start,
    end,
    isLoading,
  };
};
