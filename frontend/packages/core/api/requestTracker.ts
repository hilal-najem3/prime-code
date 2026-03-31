import { ref } from "vue";

const activeRequests = ref(0);

export const useRequestTracker = () => {
  const start = () => activeRequests.value++;
  const end = () => activeRequests.value--;

  return {
    activeRequests,
    isLoading: () => activeRequests.value > 0,
    start,
    end,
  };
};
