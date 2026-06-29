import apiClient from '../../services/apiClient'

export interface StaffPayment {
  id: number
  staff_id: number
  staff?: {
    id: number
    name: string
    email: string
  }
  salary_month: string
  total_classes: number
  salary_amount: number
  deduction_amount: number
  bonus_amount : number
  deduction_reason?: string
  final_amount: number
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

export interface StaffPaymentFormPayload {
  staff_id: number
  salary_month: string
  total_classes: number
  salary_amount: number
  bonus_amount : number
  deduction_amount: number
  deduction_reason?: string
  final_amount: number
  payment_method?: string
  payment_reference?: string
  transaction_no?: string
  payment_date?: string
  remarks?: string
  status: 'pending' | 'released'
}

export interface StaffPaymentListResponse {
  data: StaffPayment[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export const staffPaymentService = {
  getAll: (params?: Record<string, string | number | boolean>) =>
    apiClient.get<StaffPaymentListResponse>('/staff-payments', { params }),

  getById: (id: number) =>
    apiClient.get<{ data: StaffPayment }>(`/staff-payments/${id}`),

  create: (payload: StaffPaymentFormPayload) =>
    apiClient.post<StaffPayment>('/staff-payments', payload),

  update: (id: number, payload: StaffPaymentFormPayload) =>
    apiClient.put<StaffPayment>(`/staff-payments/${id}`, payload),

  remove: (id: number) => apiClient.delete<void>(`/staff-payments/${id}`),
}