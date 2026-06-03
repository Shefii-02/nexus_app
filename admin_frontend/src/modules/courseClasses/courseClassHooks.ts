import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { courseClassService, type CourseClass, type CourseClassListResponse } from './courseClassService'

export const useCourseClasses = (
  courseId: number,
  params?: Record<string, string | number | boolean>
) =>
  useQuery<CourseClassListResponse, Error>({
    queryKey: ['course-classes', courseId, params],

    queryFn: () =>
      courseClassService
        .getAll(courseId, params)
        .then((res) => res.data),

    enabled: !!courseId,
  })


export const useCourseClass = (id: number, courseId: number) => {
  // console.log('HOOK CALLED:', { id, courseId }) // ✅ works every render

  return useQuery({
    queryKey: ['course-class', id, courseId],

    queryFn: () => {
      // console.log('QUERY FN RUNNING') // ✅ runs when API triggers

      return courseClassService
        .getById(id, courseId)
        .then((r) => r.data.data)
    },

    enabled: !!id && !!courseId,
  })
}

// export const useCourseClass = (id: number, courseId: number) =>
// // {
//   // console.log(111111)
//   useQuery({
//     queryKey: ['course-class', id, courseId],
//     queryFn: () => courseClassService.getById(id, courseId).then(r => r.data.data),
//     enabled: !!id && !!courseId,
//   })
// // }

// export const useCreateCourseClass = (courseId : number) => {
//   const qc = useQueryClient()
//   return useMutation({
//     mutationFn: courseClassService.create,
//     onSuccess: () => qc.invalidateQueries({ queryKey: ['course-classes'] }),
//   })
// }
export const useCreateCourseClass = (courseId: number) => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: (payload: any) =>
      courseClassService.create(courseId, payload),

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['course-classes', courseId], // 🔥 scoped cache
      })
    },
  })
}

export const useUpdateCourseClass = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: ({ id, data, courseId }: any) =>
      courseClassService.update(courseId, id, data),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['course-classes'] }),
  })
}

export const useDeleteCourseClass = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: courseClassService.remove,
    onSuccess: () => qc.invalidateQueries({ queryKey: ['course-classes'] }),
  })
}