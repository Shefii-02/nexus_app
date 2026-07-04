import './dashboard.css'
import { AlertTriangle, RefreshCw } from 'lucide-react'
import { useDashboard } from './Usedashboard'
import StatsCards from './components/StatsCards'
import ChartSection from './components/ChartSection'
import TopCourses from './components/TopCourses'
import Notifications from './components/Notifications'
import QuickActions from './components/QuickActions'
import RevenueChart from './components/RevenueChart'
import DashboardSkeleton from './components/DashboardSkeleton'
import { useAppSelector } from '../../store/hooks'

const DashboardPage = () => {
  const user = useAppSelector((state) => state.auth.user)
  const { data, isLoading, error, refetch } = useDashboard()

  if (isLoading) {
    return <DashboardSkeleton />
  }

  if (error || !data) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-[var(--paper)] font-body">
        <div className="text-center max-w-sm px-6">
          <AlertTriangle className="mx-auto text-[var(--coral)]" size={28} />
          <p className="font-display text-lg font-semibold text-[var(--ink)] mt-3">Dashboard couldn't load</p>
          <p className="text-sm text-[var(--muted)] mt-1">{error ?? 'Something went wrong.'}</p>
          <button
            onClick={refetch}
            className="mt-4 inline-flex items-center gap-2 text-sm font-medium text-white bg-[var(--ink)] rounded-full px-4 py-2 hover:opacity-90"
          >
            <RefreshCw size={14} /> Try again
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className="p-6 space-y-6 bg-[var(--paper)] min-h-screen font-body">
      <header className="flex items-center justify-between">
        <div>
          <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">
            {new Date().toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long' })}
          </p>
          <h1 className="font-display text-2xl font-semibold text-[var(--ink)] mt-1">
            {user ? `Welcome back, ${user.name.split(' ')[0]}` : 'Welcome back'}
          </h1>
        </div>
        <button
          onClick={refetch}
          className="flex items-center gap-2 text-sm font-medium text-[var(--ink)] border border-[var(--border)] rounded-full px-4 py-2 hover:bg-white transition-colors"
        >
          <RefreshCw size={14} /> Refresh
        </button>
      </header>

      <StatsCards stats={data.stats} />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <ChartSection data={data.enrollments_chart} />
          <TopCourses courses={data.top_courses} />
        </div>

        <div className="space-y-6">
          <Notifications items={data.notifications} />
          <QuickActions />
          <RevenueChart data={data.revenue_chart} />
        </div>
      </div>
    </div>
  )
}

export default DashboardPage