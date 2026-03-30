export interface LoginPayload {
  email: string;
  password: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  meta: any;
  errors: any;
}

export interface AuthData {
  token: string;
  user: User;
  permissions: string[];
}
