import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { staffService } from './staffService'
import type { Staff, StaffFormPayload, StaffListResponse } from './staffService'

/** ========================
 * GET ALL STAFF
 ======================== */
export const useStaffMembers = (params?: Record<string, string | number | boolean>) =>
  useQuery<StaffListResponse, Error>({
    queryKey: ['staff', 'list', params],
    queryFn: () => staffService.getAll(params).then((res) => res.data),
  })

/** ========================
 * GET SINGLE STAFF
 ======================== */
export const useStaffMember = (id: number) =>
  useQuery<Staff, Error>({
    queryKey: ['staff', 'detail', id],
    queryFn: () => staffService.getById(id).then((res) => res.data.data), // ✅ FIXED
    enabled: !!id,
  })

/** ========================
 * CREATE STAFF
 ======================== */
export const useCreateStaffMember = () => {
  const queryClient = useQueryClient()

  return useMutation<Staff, Error, StaffFormPayload>({
    mutationFn: (payload) =>
      staffService.create(payload).then((res) => res.data),

    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['staff', 'list'] })
    },
  })
}

/** ========================
 * UPDATE STAFF
 ======================== */
export const useUpdateStaffMember = () => {
  const queryClient = useQueryClient()

  return useMutation<Staff, Error, { id: number; payload: StaffFormPayload }>({
    mutationFn: ({ id, payload }) =>
      staffService.update(id, payload).then((res) => res.data),

    onSuccess: (_, { id }) => {
      queryClient.invalidateQueries({ queryKey: ['staff', 'list'] })
      queryClient.invalidateQueries({ queryKey: ['staff', 'detail', id] }) // 🔥 important
    },
  })
}

/** ========================
 * DELETE STAFF
 ======================== */
export const useDeleteStaffMember = () => {
  const queryClient = useQueryClient()

  return useMutation<void, Error, number>({
    mutationFn: (id) =>
      staffService.remove(id).then(() => undefined),

    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['staff', 'list'] })
    },
  })
}