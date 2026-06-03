import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { materialService, type CourseMaterialListResponse } from './materialService'


export const useMaterials = (
  courseId: number,
  params?: Record<string, string | number | boolean>
) =>
  useQuery<CourseMaterialListResponse, Error>({
    queryKey: ['materials', courseId, params],

    queryFn: () =>
      materialService
        .getAll(courseId, params)
        .then((res) => res.data),

    enabled: !!courseId,
  })



export const useMaterial = (
  id: number,
  courseId: number
) =>
  useQuery({
    queryKey: ['material', courseId, id],
    queryFn: () =>
      materialService
        .getById(courseId, id)
        .then((r) => r.data.data),

    enabled: !!id && !!courseId,
  })

export const useCreateMaterial = (courseId: number) => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: any) =>
      materialService.create(courseId, payload),

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['materials', courseId], // 🔥 scoped cache
      })
    },
  })
}


export const useUpdateMaterial = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: ({
      courseId,
      id,
      payload,
    }: any) =>
      materialService.update(
        courseId,
        id,
        payload
      ),

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['materials'],
      })
    },
  })
}

export const useDeleteMaterial = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: ({
      courseId,
      id,
    }: {
      courseId: number
      id: number
    }) =>
      materialService.remove(courseId, id),

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['materials'],
      })
    },
  })
}

// export const useDeleteMaterial = () => {
//   const qc = useQueryClient()

//   return useMutation({
//     mutationFn: ({
//       courseId,
//       id,
//     }: any) =>
//       materialService.remove(
//         courseId,
//         id
//       ),

//     onSuccess: () => {
//       qc.invalidateQueries({
//         queryKey: ['materials'],
//       })
//     },
//   })
// }