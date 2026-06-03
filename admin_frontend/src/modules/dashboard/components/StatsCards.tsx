const stats = [
  { title: 'Total Courses', value: '128', growth: '+12.5%' },
  { title: 'Published', value: '96', growth: '+8.2%' },
  { title: 'Enrollments', value: '2,340', growth: '+18.7%' },
  { title: 'Revenue', value: '₹8,45,000', growth: '+24.6%' },
]

const StatsCards = () => {
  return (
    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
      {stats.map((s, i) => (
        <div
          key={i}
          className="bg-white p-4 rounded-2xl shadow hover:shadow-xl transition"
        >
          <p className="text-gray-500 text-sm">{s.title}</p>
          <h2 className="text-xl font-bold">{s.value}</h2>
          <span className="text-green-500 text-xs">{s.growth}</span>
        </div>
      ))}
    </div>
  )
}

export default StatsCards