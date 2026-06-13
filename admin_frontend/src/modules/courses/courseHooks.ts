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


export const useCourseTeachers = (
  courseId?: number,
  search?: string
) =>
  useQuery({
    queryKey: [
      'course-teachers',
      courseId,
      search,
    ],


    enabled: !!courseId,

    queryFn: () =>
      courseService
        .getTeachers(
          courseId!,
          search
        )
        .then(
          (res) =>
            res.data.data
        ),


  })

export const useSaveCourseTeachers = () => {

  const qc =
    useQueryClient()

  return useMutation({


    mutationFn: ({
      courseId,
      teacher_ids,
    }: {
      courseId: number
      teacher_ids: number[]
    }) =>
      courseService.saveTeachers(
        courseId,
        teacher_ids
      ),

    onSuccess: (response, variables) => {
      qc.setQueryData(
        ['course', variables.courseId],
        response.data
      )

      qc.invalidateQueries({
        queryKey: ['courses'],
      })
    }


  })
}


/** ========================
 * 🔥 SINGLE COURSE
 ======================== */
export const useCourse = (id?: number) =>
  useQuery({
    queryKey: ['course', id],
    queryFn: () =>
      courseService.getById(id!).then((res) => res.data.data),
    enabled: !!id,
    staleTime: 0,
    refetchOnWindowFocus: true,
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



export const useCourseStudents = (
  courseId?: number
) =>
  useQuery({
    queryKey: [
      'course-students',
      courseId
    ],

    queryFn: () =>
      courseService
        .getCourseStudents(courseId!)
        .then((res) => res.data),

    enabled: !!courseId,
  })


export const useCourseConversation = (
  courseId?: number
) =>
  useQuery({
    queryKey: [
      'course-conversation',
      courseId,
    ],

    queryFn: () =>
      courseService
        .getConversation(
          courseId!
        )
        .then((r) => r.data),

    enabled: !!courseId,
  })

export const useConversationParticipants = (
  courseId?: number,
  search?: string
) =>
  useQuery({
    queryKey: [
      'conversation-participants',
      courseId,
      search,
    ],

    queryFn: () =>
      courseService
        .getConversationParticipants(
          courseId!,
          search
        )
        .then((r) => r.data),

    enabled: !!courseId,
  })

export const useSaveConversation =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn: ({
        courseId,
        payload,
      }: {
        courseId: number
        payload: FormData
      }) =>
        courseService
          .saveConversation(
            courseId,
            payload
          )
          .then(
            (r) => r.data
          ),

      onSuccess: (_data, variables) => {
        qc.invalidateQueries({
          queryKey: ['course-conversation', variables.courseId],
        })

        qc.invalidateQueries({
          queryKey: ['conversation-members', variables.courseId],
        })

        qc.invalidateQueries({
          queryKey: ['conversation-participants', variables.courseId],
        })
      }
    })
  }

export const useConversationMembers = (
  courseId?: number
) =>
  useQuery({
    queryKey: [
      'conversation-members',
      courseId
    ],

    queryFn: () =>
      courseService
        .getConversationMembers(
          courseId!
        )
        .then((r) => r.data),

    enabled: !!courseId,
  })

export const useConversationUserSearch = (
  courseId?: number,
  search?: string
) =>
  useQuery({
    queryKey: [
      'conversation-users',
      courseId,
      search
    ],

    queryFn: () =>
      courseService
        .searchConversationUsers(
          courseId!,
          search
        )
        .then((r) => r.data),

    enabled:
      !!courseId &&
      search.length > 0,
  })

export const useRemoveConversationMember =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn: ({
        courseId,
        userId,
      }: {
        courseId: number
        userId: number
      }) =>
        courseService.removeConversationMember(
          courseId,
          userId
        ),

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'conversation-members'
          ]
        })
      }
    })
  }