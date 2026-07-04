import { Flame } from 'lucide-react'
import type { TopCourse } from '../Dashboard.types'

interface TopCoursesProps {
  courses: TopCourse[]
}

const TopCourses = ({ courses }: TopCoursesProps) => {
  return (
    <div className="font-body bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-sm">
      <div className="flex items-center justify-between mb-5">
        <div>
          <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">Best performers</p>
          <h3 className="font-display text-xl font-semibold text-[var(--ink)] mt-1">Top selling courses</h3>
        </div>
        <Flame size={18} className="text-[var(--gold)]" />
      </div>

      {courses.length === 0 ? (
        <p className="text-sm text-[var(--muted)] py-6 text-center">No sales recorded yet.</p>
      ) : (
        <ol className="divide-y divide-[var(--border)]">
          {courses.map((course, index) => (
            <li key={course.id} className="flex items-center justify-between py-3">
              <div className="flex items-center gap-3">
                <span className="font-mono text-xs text-[var(--muted)] w-5">{String(index + 1).padStart(2, '0')}</span>
                <div>
                  <p className="text-sm font-medium text-[var(--text)]">{course.name}</p>
                  <p className="text-xs text-[var(--muted)]">{course.sales_count} enrollments</p>
                </div>
              </div>
              <span className="font-mono text-sm font-semibold text-[var(--ink)]">{course.price}</span>
            </li>
          ))}
        </ol>
      )}
    </div>
  )
}

export default TopCourses