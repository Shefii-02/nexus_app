import {
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'

import { admissionService } from './admissionService'

export const useAdmissions = (
  params?: any
) =>
  useQuery({
    queryKey: [
      'admissions',
      params,
    ],

    queryFn: () =>
      admissionService
        .getAll(params)
        .then(
          (res) => res.data
        ),
  })

export const useAdmission = (
  id: number
) =>
  useQuery({
    queryKey: [
      'admission',
      id,
    ],

    queryFn: () =>
      admissionService
        .getById(id)
        .then(
          (res) =>
            res.data.data
        ),

    enabled: !!id,
  })

export const useCreateAdmission =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn:
        admissionService.create,

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'admissions',
          ],
        })
      },
    })
  }

export const useUpdateAdmission =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn: ({
        id,
        data,
      }: any) =>
        admissionService.update(
          id,
          data
        ),

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'admissions',
          ],
        })

        qc.invalidateQueries({
          queryKey: [
            'admission',
          ],
        })
      },
    })
  }

export const useDeleteAdmission =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn:
        admissionService.remove,

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'admissions',
          ],
        })
      },
    })
  }