// packages/media/composables/useMedia.ts

import { ref } from "vue";
import { mediaApi } from "../api/media.api";
import type { Media } from "../types/media";

export function useMedia() {
  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */
  const items = ref<Media[]>([]);
  const loading = ref(false);

  const page = ref(1);
  const lastPage = ref(1);

  const search = ref("");
  const collection = ref<string | undefined>(undefined);

  /*
  |--------------------------------------------------------------------------
  | Fetch Media
  |--------------------------------------------------------------------------
  */
  const fetchMedia = async (reset = false) => {
    if (loading.value) return;

    if (reset) {
      page.value = 1;
      items.value = [];
    }

    loading.value = true;

    try {
      const res = await mediaApi.list({
        page: page.value,
        search: search.value,
        collection: collection.value,
      });

      if (reset) {
        items.value = res.data.data;
      } else {
        items.value = [...items.value, ...res.data];
      }

      lastPage.value = res.data.meta?.last_page || 1;
    } finally {
      loading.value = false;
    }
  };

  /*
  |--------------------------------------------------------------------------
  | Load More (Infinite Scroll)
  |--------------------------------------------------------------------------
  */
  const loadMore = async () => {
    if (page.value >= lastPage.value) return;

    page.value++;
    await fetchMedia();
  };

  /*
  |--------------------------------------------------------------------------
  | Upload
  |--------------------------------------------------------------------------
  */
  const upload = async (
    file: File,
    directory: string,
    collection?: string,
    onProgress?: (progress: number) => void,
  ) => {
    const uploaded = await mediaApi.upload(
      file,
      directory,
      collection,
      onProgress,
    );

    items.value.unshift(uploaded);

    return uploaded;
  };

  /*
  |--------------------------------------------------------------------------
  | Delete
  |--------------------------------------------------------------------------
  */
  const remove = async (mediaId: number) => {
    await mediaApi.delete(mediaId);

    items.value = items.value.filter((m) => m.id !== mediaId);
  };

  /*
  |--------------------------------------------------------------------------
  | Usage
  |--------------------------------------------------------------------------
  */
  const getUsage = (mediaId: number) => {
    return mediaApi.usage(mediaId);
  };

  return {
    items,
    loading,

    page,
    lastPage,

    search,
    collection,

    fetchMedia,
    loadMore,

    upload,
    remove,
    getUsage,
  };
}
