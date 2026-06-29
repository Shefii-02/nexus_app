import apiClient from '../../services/apiClient'

export interface TeacherPayment {
  id: number
  teacher_id: number
  teacher?: {
    id: number
    name: string
    email: string
  }
  period_start: string
  period_end: string
  total_classes: number
  gross_amount: number
  deduction_amount: number
  deduction_reason?: string
  amount: number
  payment_method: string
  payment_reference?: string
  transaction_no?: string
  payment_date: string
  remarks?: string
  status: 'pending' | 'released'
  paid_at?: string
  created_by?: number
  released_by?: number
}

export interface TeacherPaymentFormPayload {
  teacher_id: number
  period_start: string
  period_end: string
  total_classes: number
  gross_amount: number
  deduction_amount: number
  deduction_reason?: string
  amount: number
  payment_method?: string
  payment_reference?: string
  transaction_no?: string
  payment_date?: string
  remarks?: string
  status: 'pending' | 'released'
}

export interface TeacherPaymentListResponse {
  data: TeacherPayment[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export const teacherPaymentService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<TeacherPaymentListResponse>('/teacher-payments', { params }),

  getById: (id: number) =>
    apiClient.get<{ data: TeacherPayment }>(`/teacher-payments/${id}`),

  create: (payload: TeacherPaymentFormPayload) =>
    apiClient.post<TeacherPayment>('/teacher-payments', payload),

  update: (id: number, payload: TeacherPaymentFormPayload) =>
    apiClient.put<TeacherPayment>(`/teacher-payments/${id}`, payload),

  remove: (id: number) => apiClient.delete<void>(`/teacher-payments/${id}`),
}