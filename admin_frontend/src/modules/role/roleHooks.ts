import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { roleService } from './roleService'

export const useRoles = () =>
  useQuery({
    queryKey: ['roles'],
    queryFn: () => roleService.getAll().then((res) => res.data),
  })

export const useRole = (id: number) =>
  useQuery({
    queryKey: ['role', id],
    queryFn: () => roleService.getById(id).then((res) => res.data.data),
    enabled: !!id,
  })

export const usePermissions = () =>
  useQuery({
    queryKey: ['permissions'],
    queryFn: () => roleService.getPermissions().then((res) => res.data.data),
  })

export const useCreateRole = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: roleService.create,
    onSuccess: () => qc.invalidateQueries({ queryKey: ['roles'] }),
  })
}

export const useUpdateRole = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: ({ id, payload }: any) =>
      roleService.update(id, payload),
    onSuccess: () => qc.invalidateQueries({ queryKey: ['roles'] }),
  })
}

export const useDeleteRole = () => {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: roleService.remove,
    onSuccess: () => qc.invalidateQueries({ queryKey: ['roles'] }),
  })
}