import apiClient from '../../services/apiClient'

export interface Student {
  id: number
  roll_number: string
  phone: string
  address: string
  guardian_name: string
  guardian_phone: string
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

export interface StudentFormPayload {
  name: string
  email: string
  phone: string
  password?: string

  roll_number: string
  address: string
  guardian_name: string
  guardian_phone: string
  status: string
}


export interface StudentListResponse {
  data: Student[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export const studentService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<StudentListResponse>('/students', { params }),
  getById: (id: number) => apiClient.get<{ data: Student }>(`/students/${id}`),
  create: (payload: StudentFormPayload) => apiClient.post<Student>('/students', payload),
  update: (id: number, payload: StudentFormPayload) => apiClient.put<Student>(`/students/${id}`, payload),
  remove: (id: number) => apiClient.delete<void>(`/students/${id}`),
}
