import http from "../http";
import type { AxiosProgressEvent } from "axios";

export const mediaApi = {
  async search(query = "", collection?: string, page = 1) {
    const res = await http.get("/media", {
      meta: {
        showLoader: true,
      },
      params: {
        search: query,
        collection,
        page,
      },
    });

    return res.data; // IMPORTANT → return full response (data + meta)
  },

  async upload(
    file: File,
    directory = "media",
    opts?: {
      collection?: string;
      disk?: "public" | "private";
      modelType?: string;
      modelId?: number | string;
      onProgress?: (progress: number) => void;
    },
  ) {
    const collection = opts?.collection;
    const onProgress = opts?.onProgress;
    const formData = new FormData();

    formData.append("file", file);
    formData.append("directory", directory);

    if (collection) {
      formData.append("collection", collection);
    }

    if (opts?.disk) {
      formData.append("disk", opts.disk);
    }

    if (opts?.modelType) {
      formData.append("model_type", opts.modelType);
    }

    if (opts?.modelId != null) {
      formData.append("model_id", String(opts.modelId));
    }

    const res = await http.post("/media/upload", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      meta: {
        showLoader: true,
      },
      onUploadProgress: (e: AxiosProgressEvent) => {
        if (!e.total) return;

        const percent = Math.round((e.loaded * 100) / e.total);
        onProgress?.(percent);
      },
    });

    return res.data.data;
  },

  async usage(id: number) {
    const res = await http.get(`/media/${id}/usage`, {
      meta: {
        showLoader: true,
      },
    });
    return res.data.data;
  },

  async delete(media_id: number) {
    const res = await http.delete("/media", {
      meta: {
        showLoader: true,
      },
      data: { media_id },
    });

    return res.data;
  },
};
