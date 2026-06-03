import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { notificationService, type UserListResponse } from './notificationService'


export const useUsers = (params?: Record<string, string | number | boolean>) =>
  useQuery<UserListResponse, Error>({
    queryKey: ['teachers', params],
    queryFn: () => notificationService.getAllUser(params).then((res) => res.data),
  })

// export const useUnreadNotificationCount = () =>
//   useQuery({
//     queryKey: ['notifications-unread-count'],
//     queryFn: () =>
//       notificationService
//         .unreadCount()
//         .then((res) => res.data.data),
//     refetchInterval: 30000, // every 30 sec
//   })

export const useNotifications =
  (params?: any) =>

    useQuery({

      queryKey: [
        'notifications',
        params
      ],

      queryFn: () =>
        notificationService
          .getAll(params)
          .then(r => r.data)
    })


export const useNotification = (
  id: number
) =>
  useQuery({
    queryKey: ['notification', id],

    queryFn: () =>
      notificationService
        .getById(id)
        .then((r) => r.data),

    enabled: !!id,
  })

export const useUpdateNotification =
  () => {
    const qc = useQueryClient()

    return useMutation({
      mutationFn: ({
        id,
        data,
      }: any) =>
        notificationService.update(
          id,
          data
        ),

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: ['notifications'],
        })
      },
    })
  }



export const useCreateNotification =
  () => {

    const qc = useQueryClient()

    return useMutation({

      mutationFn:
        notificationService.create,

      onSuccess: () => {

        qc.invalidateQueries({
          queryKey: [
            'notifications'
          ]
        })
      }
    })
  }

export const useDeleteNotification =
  () => {

    const qc = useQueryClient()

    return useMutation({

      mutationFn:
        notificationService.remove,

      onSuccess: () => {

        qc.invalidateQueries({
          queryKey: [
            'notifications'
          ]
        })
      }
    })
  }


  export const useMarkNotificationRead = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: notificationService.markRead,

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['notifications'],
      })

      qc.invalidateQueries({
        queryKey: ['notifications-unread-count'],
      })
    },
  })
}


export const useMarkAllNotificationsRead = () => {
  const qc = useQueryClient()

  return useMutation({
    mutationFn: notificationService.markAllRead,

    onSuccess: () => {
      qc.invalidateQueries({
        queryKey: ['notifications'],
      })

      qc.invalidateQueries({
        queryKey: ['notifications-unread-count'],
      })
    },
  })
}