import apiClient from '../../services/apiClient'

export interface Staff {
  id: number
  phone: string
  address: string
  department: string
  designation: string
  status: string
  user: {
    id: number
    name: string
    email: string
    status?: string
    last_activated?: string
    created_at?: string
  }
}

export interface StaffFormPayload {
  name: string
  email: string
  phone: string
  password?: string

  department: string
  address: string
  designation: string
  status: string
}


export interface StaffListResponse {
  data: Staff[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export const staffService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<StaffListResponse>('/staff', { params }),

  getById: (id: number) =>
    apiClient.get<{ data: Staff }>(`/staff/${id}`),

  create: (payload: StaffFormPayload) =>
    apiClient.post<Staff>('/staff', payload),

  update: (id: number, payload: StaffFormPayload) =>
    apiClient.put<Staff>(`/staff/${id}`, payload),

  remove: (id: number) =>
    apiClient.delete<void>(`/staff/${id}`),
}