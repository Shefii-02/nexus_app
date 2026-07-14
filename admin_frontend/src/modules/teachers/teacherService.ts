import apiClient from '../../services/apiClient'

/** ========================
 * RESPONSE TYPES
 ======================== */

export interface Teacher {
  id: number
  subject: string
  qualification: string
  experience_years: number
  address?: string
  status?: string



  user: {
    id: number
    name: string
    email: string
    phone?: string
    status?: string
    last_activated?: string
    created_at?: string
  }

}

export interface TeacherListResponse {
  data: Teacher[]
  meta?: {
    total?: number
    current_page?: number
    per_page?: number
  }
}

/** ========================
 * REQUEST PAYLOADS
 ======================== */


export interface TeacherFormPayload {
  subject: string
  qualification: string
  experience_years: number
  address?: string
  status?: string
  name: string
  email: string
  phone?: string
  password: string
}


export const teacherService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<TeacherListResponse>("/teacher/search", { params }),
  getById: (id: number) => apiClient.get<{ data: Teacher }>(`/teachers/${id}`),
  create: (payload: TeacherFormPayload) => apiClient.post<Teacher>('/teachers', payload),
  update: (id: number, payload: TeacherFormPayload) => apiClient.put<Teacher>(`/teachers/${id}`, payload),
  remove: (id: number) => apiClient.delete<void>(`/teachers/${id}`),
}
