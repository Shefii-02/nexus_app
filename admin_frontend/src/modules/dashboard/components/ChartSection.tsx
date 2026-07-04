import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts'
import type { ChartSeries } from '../Dashboard.types'

interface ChartSectionProps {
  data: ChartSeries
}

const RANGE_LABEL: Record<string, string> = {
  '7d': 'Last 7 days',
  '30d': 'Last 30 days',
  '90d': 'Last 90 days',
  '12m': 'Last 12 months',
}

const ChartSection = ({ data }: ChartSectionProps) => {
  const chartData = data.labels.map((label, i) => ({ label, value: data.values[i] }))

  return (
    <div className="font-body bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-sm">
      <div className="flex items-center justify-between mb-6">
        <div>
          <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">Overview</p>
          <h3 className="font-display text-xl font-semibold text-[var(--ink)] mt-1">Enrollments</h3>
        </div>
        <span className="text-xs font-mono text-[var(--muted)] border border-[var(--border)] rounded-full px-3 py-1">
          {RANGE_LABEL[data.range] ?? data.range}
        </span>
      </div>

      {chartData.length === 0 ? (
        <p className="text-sm text-[var(--muted)] py-16 text-center">No enrollments recorded for this period yet.</p>
      ) : (
        <ResponsiveContainer width="100%" height={260}>
          <AreaChart data={chartData} margin={{ top: 0, right: 0, left: -20, bottom: 0 }}>
            <defs>
              <linearGradient id="enrollFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stopColor="#0F6B67" stopOpacity={0.25} />
                <stop offset="100%" stopColor="#0F6B67" stopOpacity={0} />
              </linearGradient>
            </defs>
            <CartesianGrid stroke="#E7E3DA" strokeDasharray="4 4" vertical={false} />
            <XAxis
              dataKey="label"
              tick={{ fontSize: 11, fill: '#6B7280', fontFamily: 'Inter' }}
              axisLine={{ stroke: '#E7E3DA' }}
              tickLine={false}
            />
            <YAxis tick={{ fontSize: 11, fill: '#6B7280', fontFamily: 'Inter' }} axisLine={false} tickLine={false} width={40} />
            <Tooltip
              contentStyle={{ borderRadius: 12, border: '1px solid #E7E3DA', fontFamily: 'Inter', fontSize: 12 }}
            />
            <Area type="monotone" dataKey="value" stroke="#0F6B67" strokeWidth={2} fill="url(#enrollFill)" />
          </AreaChart>
        </ResponsiveContainer>
      )}
    </div>
  )
}

export default ChartSection