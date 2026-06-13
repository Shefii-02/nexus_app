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

  getConversation: (
    courseId: number
  ) =>
    apiClient.get(
      `/courses/${courseId}/conversation`
    ),
  saveConversation: (
    courseId: number,
    payload: FormData
  ) =>
    apiClient.post(
      `/courses/${courseId}/conversation`,
      payload,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    ),

  getTeachers: (
    courseId: number,
    search?: string
  ) =>
    apiClient.get(
      `/courses/${courseId}/addon-teachers`,
      {
        params: {
          search,
        },
      }
    ),


  saveTeachers: (
    courseId: number,
    teacher_ids: number[]
  ) =>
    apiClient.post(
      `/courses/${courseId}/addon-teachers`,
      {
        teacher_ids,
      }
    ),

  getCourseStudents(courseId: number) {
    return apiClient.get(
      `/courses/${courseId}/students`
    )
  },

  updateCourseStudent(
    courseId: number,
    admissionId: number,
    payload: any
  ) {
    return apiClient.put(
      `/courses/${courseId}/students/${admissionId}`,
      payload
    )
  },

  getConversationParticipants: (
    courseId: number,
    search?: string
  ) =>
    apiClient.get(
      `/courses/${courseId}/conversation/participants`,
      {
        params: {
          search,
        },
      }
    ),

  removeCourseStudent(
    courseId: number,
    admissionId: number
  ) {
    return apiClient.delete(
      `/courses/${courseId}/students/${admissionId}`
    )
  },

  bulkUpdateStudents(
    courseId: number,
    payload: any
  ) {
    return apiClient.post(
      `/courses/${courseId}/students/bulk-update`,
      payload
    )
  },

  getConversationMembers: (
    courseId: number
  ) =>
    apiClient.get(
      `/courses/${courseId}/conversation/members`
    ),

  searchConversationUsers: (
    courseId: number,
    search?: string
  ) =>
    apiClient.get(
      `/courses/${courseId}/conversation/users`,
      {
        params: { search }
      }
    ),

  removeConversationMember: (
    courseId: number,
    userId: number
  ) =>
    apiClient.delete(
      `/courses/${courseId}/conversation/members/${userId}`
    ),

}
