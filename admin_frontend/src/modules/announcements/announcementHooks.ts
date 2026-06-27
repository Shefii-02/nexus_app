import {
  useMutation,
  useQuery,
  useQueryClient,
} from '@tanstack/react-query'

import {
  announcementService,
  type AnnouncementListResponse,
} from './announcementService'

/**
 * LIST
 */
export const useAnnouncements = (
  params?: Record<
    string,
    string | number | boolean
  >
) =>
  useQuery<
    AnnouncementListResponse,
    Error
  >({
    queryKey: [
      'announcements',
      params,
    ],

    queryFn: () =>
      announcementService
        .getAll(params)
        .then(
          (res) => res.data
        ),
  })

/**
 * VIEW
 */
export const useAnnouncement = (
  id: number
) =>
  useQuery({
    queryKey: [
      'announcement',
      id,
    ],

    queryFn: () =>
      announcementService
        .getById(id)
        .then(
          (res) =>
            res.data.data
        ),

    enabled: !!id,
  })

/**
 * CREATE
 */
export const useCreateAnnouncement =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn: (payload: FormData) =>
            announcementService.create(payload).then((res) => res.data),
      // mutationFn:
      //   announcementService.create,

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'announcements',
          ],
        })
      },
    })
  }

/**
 * UPDATE
 */
export const useUpdateAnnouncement =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
       mutationFn: ({
            id,
            payload,
          }: {
            id: number
            payload: FormData
          }) => announcementService.update(id, payload).then((res) => res.data),
      

      // mutationFn: ({
      //   id,
      //   data,
      // }: any) =>
      //   announcementService.update(
      //     id,
      //     data
      //   ),

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'announcements',
          ],
        })

        qc.invalidateQueries({
          queryKey: [
            'announcement',
          ],
        })
      },
    })
  }

/**
 * DELETE
 */
export const useDeleteAnnouncement =
  () => {
    const qc =
      useQueryClient()

    return useMutation({
      mutationFn:
        announcementService.remove,

      onSuccess: () => {
        qc.invalidateQueries({
          queryKey: [
            'announcements',
          ],
        })
      },
    })
  }