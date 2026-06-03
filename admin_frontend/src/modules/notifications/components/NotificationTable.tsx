import type { Notification } from '../notificationService'

interface NotificationTableProps {
  notifications: Notification[]
  loading: boolean
  onView: (id: number) => void
}

const NotificationTable = ({ notifications, loading, onView }: NotificationTableProps) => {
  return (
    <div className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/40">
      <table className="min-w-full divide-y divide-slate-200 text-sm">
        <thead className="bg-slate-50 text-slate-700">
          <tr>
            <th className="px-6 py-4 text-left font-semibold">Title</th>
            <th className="px-6 py-4 text-left font-semibold">Type</th>
            <th className="px-6 py-4 text-left font-semibold">Priority</th>
            <th className="px-6 py-4 text-left font-semibold">Total Receivers</th>
            <th className="px-6 py-4 text-left font-semibold">Scheduled at</th>
            <th className="px-6 py-4 text-left font-semibold">Created By</th>
            <th className="px-6 py-4 text-left font-semibold">Created At</th>
            <th className="px-6 py-4 text-left font-semibold">Status</th>
            <th className="px-6 py-4 text-right font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-200">
          {loading ? (
            <tr>
              <td colSpan={4} className="p-6 text-center text-slate-500">Loading notifications...</td>
            </tr>
          ) : notifications.length === 0 ? (
            <tr>
              <td colSpan={4} className="p-6 text-center text-slate-500">No notifications found.</td>
            </tr>
          ) : (
            notifications.map((notification) => (
              <tr key={notification.id}>
                <td className="px-6 py-4 text-slate-900">{notification.title}</td>
                <td className="px-6 py-4 text-slate-900">{notification.type}</td>
                <td className="px-6 py-4 text-slate-900">{notification.priority}</td>
                <td className="px-6 py-4 text-slate-900">{notification.total_receivers}</td>
                <td className="px-6 py-4 text-slate-900">{notification.scheduled_at}</td>
                <td className="px-6 py-4 text-slate-900">{notification.created_by}</td>
                <td className="px-6 py-4 text-slate-600">{new Date(notification.created_at).toLocaleDateString()}</td>
                <td className="px-6 py-4 text-slate-600">{notification.status}</td>
                <td className="px-6 py-4 text-right text-slate-700">
                  <button
                    type="button"
                    onClick={() => onView(notification.id)}
                    className="inline-flex rounded-2xl border border-slate-200 bg-slate-100 px-3 py-1 text-sm text-slate-700 transition hover:bg-slate-200"
                  >
                    View
                  </button>
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

export default NotificationTable
