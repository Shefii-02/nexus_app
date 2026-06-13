export const useRenewalDueList = () =>
  useQuery({
    queryKey: ['renewal-due'],
    queryFn: () =>
      admissionService
        .renewalDue()
        .then((r) => r.data),
  })

export const useRenewalHistory = () =>
  useQuery({
    queryKey: ['renewal-history'],
    queryFn: () =>
      admissionService
        .renewalHistory()
        .then((r) => r.data),
  })

export const useRenewalPayment = () => {
  const qc =
    useQueryClient()

  return useMutation({
    mutationFn: (payload: any) =>
      admissionService.payRenewal(
        payload
      ),

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['renewal-due'],
      })

      qc.invalidateQueries({
        queryKey: ['renewal-history'],
      })

      qc.invalidateQueries({
        queryKey: ['admissions'],
      })
    },
  })
}