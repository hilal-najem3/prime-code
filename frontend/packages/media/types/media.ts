// packages/media/types/media.ts

export interface Media {
  id: number;
  disk: string;
  path: string;
  filename: string;
  extension: string;
  mime_type: string;
  size: number;
  alt_text?: string | null;
  collection?: string | null;
  model_type?: string | null;
  model_id?: number | null;
  user_id?: number | null;
  url: string;

  created_at?: string;
  updated_at?: string;
}

export interface MediaUsage {
  media_id: number;
  model_type: string | null;
  model_id: number | null;
  collection: string | null;
}

export interface MediaListResponse {
  data: Media[];
  meta?: {
    current_page: number;
    per_page: number;
    total: number;
    last_page?: number;
  };
}
