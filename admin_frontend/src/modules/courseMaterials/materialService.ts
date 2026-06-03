import apiClient from '../../services/apiClient'

export interface CourseMaterial {
  id: number
  course_id: number
  title: string
  description: string
  file_url: string
  material_type: string
  order: number
  status: string
}

export interface CourseMaterialListResponse {
  data: CourseMaterial[]
}

export interface MaterialPayload {
  course_id: number
  title: string
  description: string
  file_url: File | string
  material_type: string
  order: number
  status: string
}



export const materialService = {
  getAll: (courseId: number, params?: any) =>
    apiClient.get(`/courses/${courseId}/materials`, {
      params,
    }),

  getById: (courseId: number, id: number) =>
    apiClient.get(`/courses/${courseId}/materials/${id}`),

  create: (courseId: number, data: FormData) =>
    apiClient.post(
      `/courses/${courseId}/materials`,
      data,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    ),

  update: (
    courseId: number,
    id: number,
    data: FormData
  ) =>
    apiClient.post(
      `/courses/${courseId}/materials/${id}?_method=PUT`,
      data,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    ),

  remove: (courseId: number, id: number) =>
    apiClient.delete(`/courses/${courseId}/materials/${id}`),

}