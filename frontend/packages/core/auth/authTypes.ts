export interface Role {
  id: string;
  name: string;
  slug: string;
}

export interface User {
  id: string;
  name: string;
  email: string;
  enabled: number;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  roles: Role[];
  permissions: any[];
}

export interface LoginPayload {
  email: string;
  password: string;
}

export interface AuthData {
  user: User;
  role: string | null;
  permissions: string[];
  token?: string;
  access_token?: string;
  refresh_token: string;
  token_type: string;
}

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data: T;
  meta: any;
  errors: any;
}
