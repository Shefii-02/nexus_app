import {
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import { RenewalService } from './RenewalService'


export const useAdmissions = (
  params?: any
) =>
  useQuery({
    queryKey: [
      'admissions',
      params,
    ],

    queryFn: () =>
      RenewalService
        .getAll(params)
        .then(
          (res) => res.data
        ),
  })


export const useRenewalDueList = () =>
  useQuery({
    queryKey: ['renewal-due'],
    queryFn: () =>
      RenewalService.renewalDue().then((r) => r.data),
  })

export const useRenewalHistory = () =>
  useQuery({
    queryKey: ['renewal-history'],
    queryFn: () =>
      RenewalService.renewalHistory().then((r) => r.data),
  })

export const useRenewalPayment = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: any) =>
      RenewalService.payRenewal(payload),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['renewal-due'] })
      qc.invalidateQueries({ queryKey: ['renewal-history'] })
      qc.invalidateQueries({ queryKey: ['admissions'] })
    },
  })
}