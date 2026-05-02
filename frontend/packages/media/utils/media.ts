export const getMediaUrl = (
  media: any,
  variant: "thumb" | "medium" | "large" = "medium",
) => {
  return media?.variants?.[variant] || media?.url;
};
