import { Line, LineChart, ResponsiveContainer } from 'recharts'

interface SparklineProps {
  data: number[]
  color: string
}

const Sparkline = ({ data, color }: SparklineProps) => {
  const points = data.map((value, index) => ({ index, value }))

  return (
    <ResponsiveContainer width="100%" height={36}>
      <LineChart data={points} margin={{ top: 2, right: 0, bottom: 2, left: 0 }}>
        <Line
          type="monotone"
          dataKey="value"
          stroke={color}
          strokeWidth={2}
          dot={false}
          isAnimationActive={false}
        />
      </LineChart>
    </ResponsiveContainer>
  )
}

export default Sparkline