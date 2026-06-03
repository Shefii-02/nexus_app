const Notifications = () => {
  const items = [
    'New course added',
    '12 new enrollments',
    'Payout completed',
    'New review received',
  ]

  return (
    <div className="bg-white p-4 rounded-2xl shadow">
      <h3 className="font-semibold mb-4">Notifications</h3>

      {items.map((n, i) => (
        <div key={i} className="text-sm py-2 border-b">
          {n}
        </div>
      ))}

      <div className="text-blue-500 text-sm mt-2 cursor-pointer">
        View all
      </div>
    </div>
  )
}

export default Notifications