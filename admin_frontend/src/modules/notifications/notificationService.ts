import apiClient from '../../services/apiClient'

/* =========================
   TYPES
========================= */

export interface Notification {
  id: number
  user_id: number
  title: string
  message: string
  type: string
  total_receivers: string
  scheduled_at: string
  priority: string
  related_model?: string | null
  related_id?: number | null
  read_at?: string | null
  created_at: string
  updated_at: string
  created_by: string
  status:string
}

export interface NotificationListResponse {
  data: Notification[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}

export interface UnreadCountResponse {
  count: number
}


export interface User {
  id: number
  name: string
  accType: string
  email: string
  phone?: string
  status?: string
}



export interface UserListResponse {
  data: User[]
  meta?: {
    total?: number
    page?: number
    per_page?: number
  }
}


/* =========================
   CREATE REQUEST TYPE
========================= */

export interface CreateNotificationRequest {
  title: string
  message: string
  type: string
  priority: string
  related_model?: string | null
  related_id?: number | null
  scheduled_at: string
}

/* =========================
   SERVICE
========================= */

export const notificationService = {

  getAllUser: (params?: any) =>
    apiClient.get(
      '/all-users',
      { params }
    ),

  getAll: (params?: any) =>
    apiClient.get(
      '/notifications',
      { params }
    ),
  getById: (id: number) =>
    apiClient.get(`/notifications/${id}`),

  update: (
    id: number,
    data: any
  ) =>
    apiClient.put(
      `/notifications/${id}`,
      data
    ),


  create: (data: any) =>
    apiClient.post(
      '/notifications',
      data
    ),

  update: (id: number, data: any) =>
    apiClient.put(
      `/notifications/${id}`,
      data
    ),

  remove: (id: number) =>
    apiClient.delete(
      `/notifications/${id}`
    ),

  markRead: (id: number) =>
    apiClient.post(
      `/notifications/${id}/read`
    ),

  markAllRead: () =>
    apiClient.post(
      '/notifications/read-all'
    ),
  unreadCount: () =>
    apiClient.get('/notifications/unread-count'),
}