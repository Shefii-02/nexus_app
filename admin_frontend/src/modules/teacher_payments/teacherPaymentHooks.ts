import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { teacherPaymentService, type TeacherPaymentFormPayload } from './teacherPaymentService'

export const useTeacherPayments = (params?: any) =>
  useQuery({
    queryKey: ['teacher-payments', params],
    queryFn: () =>
      teacherPaymentService.getAll(params).then((res) => res.data),
  })

export const useTeacherPayment = (id: number) =>
  useQuery({
    queryKey: ['teacher-payment', id],
    queryFn: () =>
      teacherPaymentService.getById(id).then((res) => res.data.data),
    enabled: !!id,
  })

export const useCreateTeacherPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: TeacherPaymentFormPayload) =>
      teacherPaymentService.create(payload).then((r) => r.data),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['teacher-payments'] })
    },
  })
}

export const useUpdateTeacherPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: TeacherPaymentFormPayload }) =>
      teacherPaymentService.update(id, data),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['teacher-payments'] })
      qc.invalidateQueries({ queryKey: ['teacher-payment'] })
    },
  })
}

export const useDeleteTeacherPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (id: number) => teacherPaymentService.remove(id),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['teacher-payments'] })
    },
  })
}