import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { studentService } from './studentService'
import type { Student, StudentFormPayload, StudentListResponse } from './studentService'

export const useStudents = (params?: Record<string, string | number | boolean>) =>
  useQuery<StudentListResponse, Error>({
    queryKey: ['students', params],
    queryFn: () => studentService.getAll(params).then((res) => res.data),
  })

export const useStudent = (id: number) =>
  useQuery<Student, Error>({
    queryKey: ['student', id],
    queryFn: () => studentService.getById(id).then((res) => res.data.data),
    enabled: !!id,
  })

export const useCreateStudent = () => {
  const queryClient = useQueryClient()
  return useMutation<Student, Error, StudentFormPayload>({
    mutationFn: (payload) => studentService.create(payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['students'] }),
  })
}

export const useUpdateStudent = () => {
  const queryClient = useQueryClient()
  return useMutation<Student, Error, { id: number; payload: StudentFormPayload }>({
    mutationFn: ({ id, payload }) => studentService.update(id, payload).then((res) => res.data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['students'] }),
  })
}

export const useDeleteStudent = () => {
  const queryClient = useQueryClient()
  return useMutation<void, Error, number>({
    mutationFn: (id) => studentService.remove(id).then(() => undefined),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['students'] }),
  })
}
