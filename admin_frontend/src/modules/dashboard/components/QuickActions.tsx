import { FileBarChart, GraduationCap, PlusCircle, Ticket } from 'lucide-react'

const ACTIONS = [
  { label: 'Add course', icon: PlusCircle, href: '/admin/courses/create' },
  { label: 'Create coupon', icon: Ticket, href: '/admin/coupons/create' },
  { label: 'Generate report', icon: FileBarChart, href: '/admin/reports' },
  { label: 'Student list', icon: GraduationCap, href: '/admin/students' },
]

const QuickActions = () => {
  return (
    <div className="font-body bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-sm">
      <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">Shortcuts</p>
      <h3 className="font-display text-xl font-semibold text-[var(--ink)] mt-1 mb-4">Quick actions</h3>

      <div className="grid grid-cols-2 gap-3">
        {ACTIONS.map(({ label, icon: Icon, href }) => (
          <a
            key={label}
            href={href}
            className="group flex flex-col items-start gap-2 rounded-xl border border-[var(--border)] p-3 hover:border-[var(--ink)] hover:bg-[var(--paper)] transition-colors"
          >
            <Icon size={18} className="text-[var(--ink)]" strokeWidth={1.75} />
            <span className="text-sm font-medium text-[var(--text)] group-hover:text-[var(--ink)]">{label}</span>
          </a>
        ))}
      </div>
    </div>
  )
}

export default QuickActions