// packages/media/api/media.api.ts

import http from "@core/api/http";
import type { Media, MediaUsage, MediaListResponse } from "../types/media";
import type { AxiosProgressEvent } from "axios";

class MediaApi {
  /*
  |--------------------------------------------------------------------------
  | List Media (with pagination + search)
  |--------------------------------------------------------------------------
  */
  async list(params: {
    page?: number;
    per_page?: number;
    search?: string;
    collection?: string;
  }): Promise<MediaListResponse> {
    const res = await http.get("/media", { params });

    return {
      data: res.data.data,
      meta: res.data.meta,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Upload Media
  |--------------------------------------------------------------------------
  */
  async upload(
    file: File,
    directory: string,
    collection?: string,
    disk: "public" | "private" = "public",
    onProgress?: (progress: number) => void,
  ): Promise<Media> {
    const formData = new FormData();

    formData.append("file", file);
    formData.append("directory", directory);
    formData.append("disk", disk);

    if (collection) {
      formData.append("collection", collection);
    }

    const res = await http.post("/media/upload", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress: (event: AxiosProgressEvent) => {
        if (!onProgress) return;
        if (!event.total) return;

        const percent = Math.round((event.loaded * 100) / event.total);

        onProgress(percent);
      },
    });

    console.log("UPLOAD RESPONSE", res);

    return res.data;
  }

  /*
  |--------------------------------------------------------------------------
  | Attach Existing Media
  |--------------------------------------------------------------------------
  */
  async attach(payload: {
    media_id: number;
    model_type: string;
    model_id: number;
    collection?: string;
  }): Promise<Media> {
    const res = await http.post("/media/attach", payload);

    return res.data;
  }

  /*
  |--------------------------------------------------------------------------
  | Delete Media
  |--------------------------------------------------------------------------
  */
  async delete(mediaId: string): Promise<void> {
    await http.delete("/media", {
      data: {
        media_id: mediaId,
      },
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Get Media Usage
  |--------------------------------------------------------------------------
  */
  async usage(mediaId: string): Promise<MediaUsage[]> {
    const res = await http.get(`/media/${mediaId}/usage`);

    return res.data;
  }
}

export const mediaApi = new MediaApi();
