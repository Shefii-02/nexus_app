import { Bell } from 'lucide-react'
import { useUnreadNotificationCount } from '../notifications/notificationHooks'

const NotificationBell = () => {
  const { data } = useUnreadNotificationCount()

  const count = data?.count || 0

  return (
    <div className="relative cursor-pointer">
      <Bell size={22} />

      {count > 0 && (
        <span
          className="
            absolute
            -top-2
            -right-2
            min-w-[18px]
            h-[18px]
            px-1
            rounded-full
            bg-red-500
            text-white
            text-xs
            flex
            items-center
            justify-center
          "
        >
          {count > 99 ? '99+' : count}
        </span>
      )}
    </div>
  )
}

export default NotificationBell