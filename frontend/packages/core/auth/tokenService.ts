const ACCESS_TOKEN = "access_token";
const REFRESH_TOKEN = "refresh_token";

export const tokenService = {
  get() {
    return localStorage.getItem(ACCESS_TOKEN);
  },

  getRefresh() {
    return localStorage.getItem(REFRESH_TOKEN);
  },

  set(access: string, refresh: string) {
    localStorage.setItem(ACCESS_TOKEN, access);
    localStorage.setItem(REFRESH_TOKEN, refresh);
  },

  remove() {
    localStorage.removeItem(ACCESS_TOKEN);
    localStorage.removeItem(REFRESH_TOKEN);
  },
};
