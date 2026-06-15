import {
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import { renewalService } from './renewalService'


export const useAdmissions = (
  params?: any
) =>
  useQuery({
    queryKey: [
      'admissions',
      params,
    ],

    queryFn: () =>
      renewalService
        .getAll(params)
        .then(
          (res) => res.data
        ),
  })


export const useRenewalDueList = () =>
  useQuery({
    queryKey: ['renewal-due'],
    queryFn: () =>
      renewalService.renewalDue().then((r) => r.data),
  })

export const useRenewalHistory = () =>
  useQuery({
    queryKey: ['renewal-history'],
    queryFn: () =>
      renewalService.renewalHistory().then((r) => r.data),
  })

export const useRenewalPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: any) =>
      renewalService.payRenewal(payload),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['renewal-due'] })
      qc.invalidateQueries({ queryKey: ['renewal-history'] })
      qc.invalidateQueries({ queryKey: ['admissions'] })
    },
  })
}