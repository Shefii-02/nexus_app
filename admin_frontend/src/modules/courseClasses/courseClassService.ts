import apiClient from '../../services/apiClient'
import type { TeacherListResponse } from '../teachers/teacherService'

export interface CourseClass {
  id: number
  course_id: number
  teacher_id: number
  title: string
  description: string
  class_link: string
  record_link: string
  source: string
  class_number: number
  scheduled_date: string
  started_at: string
  ended_at: string
  duration_minutes: number
  room_location: string
  status: string
}

export interface CourseClassListResponse {
  data: CourseClass[]
}

export interface CourseClassPayload {
  course_id: number
  teacher_id: number
  title: string
  description: string
  class_link: string
  record_link: string
  source: string
  class_number: number
  scheduled_date: string
  started_at: string
  ended_at: string
  duration_minutes: number
  room_location: string
  status: string
}

export const courseClassService = {
  getAll: (courseId: number, params?: any) =>
    apiClient.get<CourseClassListResponse>(`/courses/${courseId}/classes`, { params }),

  getById: (id: number, courseId: number) => {
    const url = `/courses/${courseId}/classes/${id}`

    return apiClient.get<{ data: CourseClass }>(url) // ✅ MUST RETURN
  },
  getCourseTeachers: (courseId: number, params?: Record<string, string | number | boolean>) => {
    const url = `/courses/${courseId}/teachers`;

    // Pass params inside the options configuration object as the second argument
    return apiClient.get<{ data: TeacherListResponse }>(url, { params });
  },


  // getById: (id: number, courseId: number) =>{
  //     console.log('API URL:', `/courses/${courseId}/classes/${id}`) // ✅ print here
  //   apiClient.get<{ data: CourseClass }>(`/courses/${courseId}/classes/${id}`)
  // },

  create: (courseId: number, payload: CourseClassPayload) =>
    apiClient.post(`/courses/${courseId}/classes`, payload),

  // create: (data: CourseClassPayload,courseId : number) =>
  //   apiClient.post(`/courses/${courseId}/classes`, data),

  // update: (courseId: number, id: number, data: CourseClassPayload) =>{
  //    console.log(courseId),
  //   return apiClient.put(`/courses/${courseId}/classes/${id}`, data),
  // },


  update: (courseId: number, id: number, data: CourseClassPayload) => {
    return apiClient.put(
      `/courses/${courseId}/classes/${id}`,
      data
    )
  },

  remove: (courseId: number, id: number) =>
    apiClient.delete(`/courses/${courseId}/classes/${id}`),
}