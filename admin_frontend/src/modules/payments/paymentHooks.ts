import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { paymentService } from './paymentService'
import type { Payment, PaymentPayload, PaymentListResponse } from './paymentService'

export const usePayments = (params?: Record<string, string | number | boolean>) =>
  useQuery<PaymentListResponse, Error>({
    queryKey: ['payments', params],
    queryFn: () => paymentService.getAll(params).then((res) => res.data),
  })

export const usePayment = (id: number) =>
  useQuery<Payment, Error>({
    queryKey: ['payment', id],
    queryFn: () => paymentService.getById(id).then((res) => res.data),
    enabled: !!id,
  })

export const useCreatePayment = () => {
  const queryClient = useQueryClient()
  return useMutation<Payment, Error, PaymentPayload>({
    mutationFn: (payload) => paymentService.create(payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export const useUpdatePayment = () => {
  const queryClient = useQueryClient()
  return useMutation<Payment, Error, { id: number; payload: PaymentPayload }>({
    mutationFn: ({ id, payload }) => paymentService.update(id, payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export const useDeletePayment = () => {
  const queryClient = useQueryClient()
  return useMutation<void, Error, number>({
    mutationFn: (id) => paymentService.remove(id).then(() => undefined),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export const useVerifyPayment = () => {
  const queryClient = useQueryClient()
  return useMutation<void, Error, number>({
    mutationFn: (id) => paymentService.verify(id).then(() => undefined),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export const useRejectPayment = () => {
  const queryClient = useQueryClient()
  return useMutation<void, Error, number>({
    mutationFn: (id) => paymentService.reject(id).then(() => undefined),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['payments'] }),
  })
}

export const useStudentPayments = (studentId?: number) =>
  useQuery<PaymentListResponse, Error>({
    queryKey: ['payments', 'student', studentId],
    queryFn: () => paymentService.getStudentPayments(studentId!).then((res) => res.data),
    enabled: !!studentId,
  })
