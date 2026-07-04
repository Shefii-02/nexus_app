const pulse = 'animate-pulse bg-[var(--border)]/60 rounded-xl'

const DashboardSkeleton = () => {
  return (
    <div className="p-6 space-y-6 bg-[var(--paper)] min-h-screen font-body">
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {Array.from({ length: 4 }).map((_, i) => (
          <div key={i} className={`h-32 ${pulse}`} />
        ))}
      </div>
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className={`h-80 ${pulse}`} />
          <div className={`h-56 ${pulse}`} />
        </div>
        <div className="space-y-6">
          <div className={`h-56 ${pulse}`} />
          <div className={`h-40 ${pulse}`} />
          <div className={`h-56 ${pulse}`} />
        </div>
      </div>
    </div>
  )
}

export default DashboardSkeleton