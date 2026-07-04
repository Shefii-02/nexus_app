import { BookOpen, IndianRupee, ListChecks, Users } from 'lucide-react'
import Sparkline from './Sparkline'
import type { DashboardStats } from '../Dashboard.types'

interface StatsCardsProps {
  stats: DashboardStats
}

const CARD_META = [
  { key: 'total_courses', label: 'Total courses', icon: BookOpen },
  { key: 'total_students', label: 'Total students', icon: Users },
  { key: 'enrollments', label: 'Enrollments', icon: ListChecks },
  { key: 'revenue', label: 'Revenue', icon: IndianRupee },
] as const

const StatsCards = ({ stats }: StatsCardsProps) => {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      {CARD_META.map(({ key, label, icon: Icon }) => {
        const metric = stats[key]
        const isPositive = !metric.growth.startsWith('-')
        const displayValue =
          key === 'revenue' ? metric.formatted ?? `₹${metric.value}` : metric.value.toLocaleString('en-IN')

        return (
          <article
            key={key}
            className="font-body relative bg-[var(--card)] border border-[var(--border)] rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow"
          >
            <div className="flex items-start justify-between">
              <div>
                <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">{label}</p>
                <p className="font-display mt-2 text-3xl font-semibold text-[var(--ink)]">{displayValue}</p>
              </div>
              <span className="flex h-9 w-9 items-center justify-center rounded-full bg-[var(--paper)] text-[var(--ink)]">
                <Icon size={18} strokeWidth={1.75} />
              </span>
            </div>

            <div className="mt-3 flex items-center gap-2">
              <span
                className={`font-mono text-xs font-medium px-1.5 py-0.5 rounded ${
                  isPositive ? 'text-[var(--gold)] bg-[var(--gold)]/10' : 'text-[var(--coral)] bg-[var(--coral)]/10'
                }`}
              >
                {metric.growth}
              </span>
              <span className="text-xs text-[var(--muted)]">vs last 30 days</span>
            </div>

            <div className="mt-3 -mx-1">
              <Sparkline data={metric.trend} color={isPositive ? '#C9A227' : '#E4572E'} />
            </div>
          </article>
        )
      })}
    </div>
  )
}

export default StatsCards