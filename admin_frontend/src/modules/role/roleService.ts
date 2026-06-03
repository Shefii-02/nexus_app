import apiClient from '../../services/apiClient'

export interface Permission {
  id: number
  name: string
  label: string
}

export interface Role {
  id: number
  name: string
  permissions: Permission[]
}

export interface RoleFormPayload {
  name: string
  permissions: string[]
}

export interface RoleListResponse {
  data: Role[]
}

export const roleService = {
  getAll: () => apiClient.get<RoleListResponse>('/roles'),
  getById: (id: number) => apiClient.get<{ data: Role }>(`/roles/${id}`),

  getPermissions: () =>
    apiClient.get<{ data: Permission[] }>('/permissions'),

  create: (payload: RoleFormPayload) =>
    apiClient.post<Role>('/roles', payload),

  update: (id: number, payload: RoleFormPayload) =>
    apiClient.put<Role>(`/roles/${id}`, payload),

  remove: (id: number) =>
    apiClient.delete<void>(`/roles/${id}`),
}