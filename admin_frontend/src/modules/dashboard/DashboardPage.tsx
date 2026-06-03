// import { useAppSelector } from '../../../store/hooks'

// const DashboardPage = () => {
//   const user = useAppSelector((state) => state.auth.user)

//   return (
//     <section className="space-y-8">
//       <div className="grid gap-6 xl:grid-cols-3">
//         <article className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
//           <p className="text-sm text-slate-500">Total students</p>
//           <p className="mt-4 text-3xl font-semibold text-slate-900">—</p>
//         </article>
//         <article className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
//           <p className="text-sm text-slate-500">Total teachers</p>
//           <p className="mt-4 text-3xl font-semibold text-slate-900">—</p>
//         </article>
//         <article className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
//           <p className="text-sm text-slate-500">Payments summary</p>
//           <p className="mt-4 text-3xl font-semibold text-slate-900">—</p>
//         </article>
//       </div>
//       <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
//         <h3 className="text-xl font-semibold text-slate-900">Recent announcements</h3>
//         <p className="mt-3 text-sm leading-6 text-slate-600">Announcements and key updates will appear here once loaded from the API.</p>
//       </div>
//       <div className="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
//         <p className="font-medium text-slate-900">User</p>
//         <p className="mt-2">{user ? `${user.name} (${user.email})` : 'Loading your profile...'}</p>
//       </div>
//     </section>
//   )
// }

// export default DashboardPage


import StatsCards from './components/StatsCards'
import ChartSection from './components/ChartSection'
import TopCourses from './components/TopCourses'
import Notifications from './components/Notifications'
import QuickActions from './components/QuickActions'
import RevenueChart from './components/RevenueChart'

const DashboardPage = () => {
  return (
    <div className="p-6 space-y-6 bg-gray-100 min-h-screen">

      {/* TOP CARDS */}
      <StatsCards />

      {/* MAIN GRID */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {/* LEFT */}
        <div className="lg:col-span-2 space-y-6">
          <ChartSection />
          <TopCourses />
        </div>

        {/* RIGHT */}
        <div className="space-y-6">
          <Notifications />
          <QuickActions />
          <RevenueChart />
        </div>
      </div>
    </div>
  )
}

export default DashboardPage