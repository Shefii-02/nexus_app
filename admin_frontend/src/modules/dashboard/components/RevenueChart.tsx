import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts'
import type { ChartSeries } from '../Dashboard.types'

interface RevenueChartProps {
  data: ChartSeries
}

const COLORS = ['#14213D', '#0F6B67', '#C9A227', '#E4572E', '#8894A8']

const RevenueChart = ({ data }: RevenueChartProps) => {
  // const chartData = data.labels.map((label, i) => ({ name: label, value: data.values[i] }))
  // const total = chartData.reduce((sum, item) => sum + item.value, 0)

  return (
    <div className="font-body bg-[var(--card)] border border-[var(--border)] rounded-2xl p-6 shadow-sm">
      <p className="text-[11px] font-mono uppercase tracking-[0.14em] text-[var(--muted)]">Breakdown</p>
      <h3 className="font-display text-xl font-semibold text-[var(--ink)] mt-1 mb-4">Revenue by category</h3>
  {/*
      {total === 0 ? (
        <p className="text-sm text-[var(--muted)] py-10 text-center">No revenue recorded for this period yet.</p>
      ) : (
        <>
         <ResponsiveContainer width="100%" height={200}>
            <PieChart>
              <Pie data={chartData} dataKey="value" nameKey="name" innerRadius={55} outerRadius={80} paddingAngle={2}>
                {chartData.map((_, index) => (
                  <Cell key={index} fill={COLORS[index % COLORS.length]} stroke="none" />
                ))}
              </Pie>
              <Tooltip
                formatter={(value: number) => `₹${value.toLocaleString('en-IN')}`}
                contentStyle={{ borderRadius: 12, border: '1px solid #E7E3DA', fontFamily: 'Inter', fontSize: 12 }}
              />
            </PieChart>
          </ResponsiveContainer> */}

          {/* <ul className="mt-2 space-y-2">
            {chartData.map((item, index) => (
              <li key={item.name} className="flex items-center justify-between text-sm">
                <span className="flex items-center gap-2 text-[var(--text)]">
                  <span className="h-2.5 w-2.5 rounded-full" style={{ backgroundColor: COLORS[index % COLORS.length] }} />
                  {item.name}
                </span>
                <span className="font-mono text-[var(--muted)]">{((item.value / total) * 100).toFixed(0)}%</span>
              </li>
            ))}
          </ul> 
        </>
      )} */}
    </div>
  )
}

export default RevenueChart