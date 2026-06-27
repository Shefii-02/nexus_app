import apiClient from '../../services/apiClient'

export interface Announcement {
  id: number
  title: string
  content: string
  created_by: number

  thumbnail?: string

  target_type:
  | 'all'
  | 'students'
  | 'teachers'
  | 'staff'

  start_date: string
  end_date: string

  priority:
  | 'low'
  | 'medium'
  | 'high'
  | 'urgent'

  status:
  | 'active'
  | 'inactive'

  created_at?: string
  updated_at?: string
}

export interface AnnouncementPayload {
  title: string
  content: string

  target_type: string

  start_date: string
  end_date: string

  priority: string
  status: string
}

export interface AnnouncementListResponse {
  data: Announcement[]
  meta?: any
}

export const announcementService = {
  getAll: (params?: any) =>
    apiClient.get(
      '/announcements',
      {
        params,
      }
    ),

  getById: (id: number) =>
    apiClient.get(
      `/announcements/${id}`
    ),

  create: (
    data: FormData
    // data: AnnouncementPayload
  ) =>
    apiClient.post(
      '/announcements', data,
      {
        headers: { 'Content-Type': 'multipart/form-data' },
      }

    ),

  update: (
    id: number,
    data: FormData
    // data: AnnouncementPayload
  ) =>
    apiClient.put(
      `/announcements/${id}?_method=PUT`,
      data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }
    ),

  remove: (id: number) =>
    apiClient.delete(
      `/announcements/${id}`
    ),
}