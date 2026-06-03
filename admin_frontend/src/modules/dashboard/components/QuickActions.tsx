const actions = [
  'Add Course',
  'Create Coupon',
  'Generate Report',
  'Student List',
]

const QuickActions = () => {
  return (
    <div className="bg-white p-4 rounded-2xl shadow">
      <h3 className="font-semibold mb-4">Quick Actions</h3>

      <div className="grid grid-cols-2 gap-2">
        {actions.map((a, i) => (
          <button
            key={i}
            className="bg-gray-100 p-2 rounded-lg hover:bg-gray-200"
          >
            {a}
          </button>
        ))}
      </div>
    </div>
  )
}

export default QuickActions