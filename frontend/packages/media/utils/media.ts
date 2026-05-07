// path to: frontend/packages/media/utils/media.ts
export const getMediaUrl = (
  media: any,
  variant: "thumb" | "medium" | "large" = "medium",
) => {
  if (!media) return "";

  /*
  |--------------------------------------------------------------------------
  | Private Media
  |--------------------------------------------------------------------------
  */

  if (media.disk === "private") {
    return media.url;
  }

  /*
  |--------------------------------------------------------------------------
  | Public Variants
  |--------------------------------------------------------------------------
  */

  return media?.variants?.[variant] || media?.url || "";
};
