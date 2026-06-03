import type { ReactNode } from 'react'

interface PageHeaderProps {
  title: string
  subtitle?: string
  onBack?: () => void
  actions?: ReactNode
}

const PageHeader = ({ title, subtitle, onBack, actions }: PageHeaderProps) => {
  return (
    <div className="rounded-3xl border bg-white p-6 shadow-sm flex justify-between items-center">

      {/* LEFT */}
      <div className="flex items-center gap-3">
        {onBack && (
          <button
            onClick={onBack}
            className="px-3 py-1 bg-slate-100 rounded-xl text-sm hover:bg-slate-200"
          >
            ←
          </button>
        )}

        <div>
          <h2 className="text-xl font-semibold">{title}</h2>
          {subtitle && (
            <p className="text-sm text-gray-500">{subtitle}</p>
          )}
        </div>
      </div>

      {/* RIGHT */}
      {actions && (
        <div className="flex items-center gap-2">
          {actions}
        </div>
      )}
    </div>
  )
}

export default PageHeader