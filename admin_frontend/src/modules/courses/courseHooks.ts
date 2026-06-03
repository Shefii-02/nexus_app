import {
  useInfiniteQuery,
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'
import { courseService } from './courseService'

/** ========================
 * 🔥 INFINITE LIST
 ======================== */
export const useInfiniteCourses = (params?: any) =>
  useInfiniteQuery({
    queryKey: ['courses', params],

    queryFn: ({ pageParam = 1 }) =>
      courseService.getAll({ ...params, page: pageParam }),

    getNextPageParam: (lastPage) => {
      const current = lastPage.pagination.current_page
      const last = lastPage.pagination.last_page

      return current < last ? current + 1 : undefined
    },

    initialPageParam: 1,
  })

/** ========================
 * 🔥 SINGLE COURSE
 ======================== */
export const useCourse = (id?: number) =>
  useQuery({
    queryKey: ['course', id],
    queryFn: () =>
      courseService.getById(id!).then((res) => res.data.data),
    enabled: !!id,
  })

/** ========================
 * 🔥 CREATE
 ======================== */
export const useCreateCourse = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: FormData) =>
      courseService.create(payload).then((res) => res.data),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['courses'] })
    },
  })
}

/** ========================
 * 🔥 UPDATE
 ======================== */
export const useUpdateCourse = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: ({
      id,
      payload,
    }: {
      id: number
      payload: FormData
    }) => courseService.update(id, payload).then((res) => res.data),

    onSuccess: (_data, variables) => {
      qc.invalidateQueries({ queryKey: ['courses'] })
      qc.invalidateQueries({ queryKey: ['course', variables.id] })
    },
  })
}

/** ========================
 * 🔥 DELETE
 ======================== */
export const useDeleteCourse = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (id: number) =>
      courseService.remove(id).then(() => undefined),

    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ['courses'] })
    },
  })
}