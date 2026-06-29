import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { staffPaymentService, type StaffPaymentFormPayload } from './staffPaymentService'

export const useStaffPayments = (params?: any) =>
  useQuery({
    queryKey: ['staff-payments', params],
    queryFn: () =>
      staffPaymentService.getAll(params).then((res) => res.data),
  })

export const useStaffPayment = (id: number) =>
  useQuery({
    queryKey: ['staff-payment', id],
    queryFn: () =>
      staffPaymentService.getById(id).then((res) => res.data.data),
    enabled: !!id,
  })

export const useCreateStaffPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: StaffPaymentFormPayload) =>
      staffPaymentService.create(payload).then((r) => r.data),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['staff-payments'] })
    },
  })
}

export const useUpdateStaffPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: StaffPaymentFormPayload }) =>
      staffPaymentService.update(id, data),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['staff-payments'] })
      qc.invalidateQueries({ queryKey: ['staff-payment'] })
    },
  })
}

export const useDeleteStaffPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (id: number) => staffPaymentService.remove(id),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['staff-payments'] })
    },
  })
}