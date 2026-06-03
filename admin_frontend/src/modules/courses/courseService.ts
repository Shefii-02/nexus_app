import apiClient from '../../services/apiClient'

export interface Course {
  id: number
  code: string
  name: string
  description?: string
  thumbnail?: string

  actual_price?: number
  net_price?: number

  class_type: 'online' | 'offline' | 'hybrid'
  status: 'active' | 'inactive' | 'archived'

  started_at?: string
  ended_at?: string

  teacher_id?: number
}

export interface Pagination {
  current_page: number
  last_page: number
  total: number
  per_page: number
}

export const courseService = {
  getAll: async (params?: any) => {
    const res = await apiClient.get('/courses', { params })

    return {
      data: res.data.data,
      pagination: res.data.pagination,
    }
  },

  getById: (id: number) =>
    apiClient.get(`/courses/${id}`),

  create: (payload: FormData) =>
    apiClient.post('/courses', payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),

  update: (id: number, payload: FormData) =>
    apiClient.post(`/courses/${id}?_method=PUT`, payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),

  remove: (id: number) =>
    apiClient.delete(`/courses/${id}`),
}