import { useMemo, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import NotificationTable from '../components/NotificationTable'
import { useNotifications } from '../notificationHooks'

const NotificationsPage = () => {
  const navigate = useNavigate()
  const [search, setSearch] = useState('')
  const { data, isLoading, error } = useNotifications()
  // const { data: unreadCount } = useUnreadNotificationCount()


    const notifications = (data?.data || []).filter((noti) =>
    noti?.title?.toLowerCase().includes(search.toLowerCase()) ||
    noti?.message?.toLowerCase().includes(search.toLowerCase())
  )

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold text-slate-900">Notifications</h2>
          <p className="mt-1 text-sm text-slate-500">Review notifications and unread counts from the API.</p>
        </div>
        <div className="flex flex-wrap items-center gap-3">
          {/* <div className="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-700"> */}
            {/* Unread: {unreadCount?.count ?? '—'} */}
          {/* </div> */}
          <input
            value={search}
            onChange={(event) => setSearch(event.target.value)}
            placeholder="Search notifications"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 sm:w-80"
          />
          <button
            type="button"
            onClick={() => navigate('/notifications/create')}
            className="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
          >
            New notification
          </button>
        </div>
      </div>

      {error ? (
        <div className="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">Unable to load notifications.</div>
      ) : null}

      <NotificationTable notifications={notifications} loading={isLoading} onView={(id) => navigate(`/notifications/${id}`)} />
    </div>
  )
}

export default NotificationsPage
