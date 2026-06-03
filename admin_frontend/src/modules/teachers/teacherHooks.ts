import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { teacherService } from './teacherService'
import type { Teacher, TeacherFormPayload, TeacherListResponse } from './teacherService'

export const useTeachers = (params?: Record<string, string | number | boolean>) =>
  useQuery<TeacherListResponse, Error>({
    queryKey: ['teachers', params],
    queryFn: () => teacherService.getAll(params).then((res) => res.data),
  })

export const useTeacher = (id: number) =>
  useQuery<Teacher, Error>({
    queryKey: ['teacher', id],
    queryFn: () => teacherService.getById(id).then((res) => res.data.data),
    enabled: !!id,
  })

export const useCreateTeacher = () => {
  const queryClient = useQueryClient()
  return useMutation<Teacher, Error, TeacherFormPayload>({
    mutationFn: (payload) => teacherService.create(payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teachers'] }),
  })
}

export const useUpdateTeacher = () => {
  const queryClient = useQueryClient()
  return useMutation<Teacher, Error, { id: number; payload: TeacherFormPayload }>({
    mutationFn: ({ id, payload }) => teacherService.update(id, payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teachers'] }),
  })
}

export const useDeleteTeacher = () => {
  const queryClient = useQueryClient()
  return useMutation<void, Error, number>({
    mutationFn: (id) => teacherService.remove(id).then(() => undefined),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teachers'] }),
  })
}
