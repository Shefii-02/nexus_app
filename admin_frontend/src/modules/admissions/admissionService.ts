import apiClient from '../../services/apiClient'

export const admissionService = {
  getAll: (params?: any) =>
    apiClient.get('/admissions', {
      params,
    }),

  getById: (id: number) =>
    apiClient.get(`/admissions/${id}`),

  create: (payload: any) =>
  apiClient.post(
    '/admissions',
    payload
  ),

  update: (
    id: number,
    data: any
  ) =>
    apiClient.put(
      `/admissions/${id}`,
      data
    ),

  remove: (id: number) =>
    apiClient.delete(
      `/admissions/${id}`
    ),
}