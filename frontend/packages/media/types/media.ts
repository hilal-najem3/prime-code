// packages/media/types/media.ts

export interface Media {
  id: string;
  disk: string;
  path: string;
  filename: string;
  extension: string;
  mime_type: string;
  size: number;
  alt_text?: string | null;
  collection?: string | null;
  model_type?: string | null;
  model_id?: string | null;
  user_id?: string | null;

  url: string;

  variants?: Record<string, string>;

  created_at?: string;
  updated_at?: string;
}

export interface MediaUsage {
  media_id: string;
  model_type: string | null;
  model_id: string | null;
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
