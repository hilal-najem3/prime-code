import "axios";

declare module "axios" {
  export interface AxiosRequestConfig {
    meta?: {
      showLoader?: boolean;
      showSuccessToast?: boolean;
      showErrorToast?: boolean;
    };
  }
}
