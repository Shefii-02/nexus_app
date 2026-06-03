import apiClient from '../../services/apiClient'

export interface PaymentPayload {
  student_id: number
  course_id: number
  amount: number
  method?: string
}

export interface Payment {
  id: number
  student_name: string
  course_title: string
  amount: number
  status: string
  paid_at: string
}

export interface PaymentListResponse {
  data: Payment[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export const paymentService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<PaymentListResponse>('/payments', { params }),
  getById: (id: number) => apiClient.get<Payment>(`/payments/${id}`),
  create: (payload: PaymentPayload) => apiClient.post<Payment>('/payments', payload),
  update: (id: number, payload: PaymentPayload) => apiClient.put<Payment>(`/payments/${id}`, payload),
  remove: (id: number) => apiClient.delete<void>(`/payments/${id}`),
  verify: (id: number) => apiClient.post(`/payments/${id}/verify`),
  reject: (id: number) => apiClient.post(`/payments/${id}/reject`),
  getStudentPayments: (studentId: number) =>
    apiClient.get<PaymentListResponse>(`/payments/student/${studentId}`),
}
