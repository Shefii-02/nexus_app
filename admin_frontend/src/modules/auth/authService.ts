import apiClient from '../../services/apiClient'

export interface LoginCredentials {
  email: string
  password: string
}

export interface UserProfile {
  id: number
  name: string
  email: string
  role: 'admin' | 'Staff' | string
  acc_type: 'admin' | 'Staff' | string
  
}

export interface LoginResponse {
  access_token: string
  refresh_token?: string
  token_type?: string
  user?: UserProfile
}

export interface RefreshTokenResponse {
  access_token: string
  token_type?: string
  expires_in?: number
  user?: UserProfile
}

export const authService = {
  login: (credentials: LoginCredentials) =>
    apiClient.post<LoginResponse>('/auth/login', credentials),
  getProfile: () => apiClient.get<UserProfile>('/profile'),
  refresh: () => apiClient.post<RefreshTokenResponse>('/auth/refresh'),
}
