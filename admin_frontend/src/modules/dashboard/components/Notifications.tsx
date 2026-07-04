import { Bell, BookPlus, Star, Users, Wallet } from 'lucide-react'
import type { NotificationItem } from '../Dashboard.types'

interface NotificationsProps {
  items: NotificationItem[]
}

const ICONS: Record<string, typeof Bell> = {
  course: BookPlus,
  enrollment: Users,
  payment: Wallet,
  review: Star,
}

const Notifications = ({ items }: NotificationsProps) => {
  return (
    <div className="font-body bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-sm">
      <div className="flex items-center justify-between mb-4">
        <div>
          <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">Activity</p>
          <h3 className="font-display text-xl font-semibold text-[var(--ink)] mt-1">Notifications</h3>
        </div>
        <Bell size={18} className="text-[var(--muted)]" />
      </div>

      {items.length === 0 ? (
        <p className="text-sm text-[var(--muted)] py-6 text-center">You're all caught up.</p>
      ) : (
        <ul className="space-y-1">
          {items.slice(0, 5).map((item) => {
            const Icon = ICONS[item.type] ?? Bell
            return (
              <li key={item.id} className="flex items-start gap-3 py-2.5 border-b border-[var(--border)] last:border-0">
                <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[var(--paper)] text-[var(--ink)]">
                  <Icon size={14} strokeWidth={1.75} />
                </span>
                <div className="min-w-0">
                  <p className="text-sm text-[var(--text)] truncate">{item.message}</p>
                  <p className="text-xs text-[var(--muted)]">{item.created_at}</p>
                </div>
              </li>
            )
          })}
        </ul>
      )}

      <button className="mt-3 text-sm font-medium text-[var(--teal)] hover:underline">View all</button>
    </div>
  )
}

export default Notifications