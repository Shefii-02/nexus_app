const TopCourses = () => {
  const courses = [
    { name: 'React Bootcamp', price: '₹1999' },
    { name: 'Python Course', price: '₹1499' },
    { name: 'UI/UX Design', price: '₹1299' },
  ]

  return (
    <div className="bg-white p-4 rounded-2xl shadow">
      <h3 className="font-semibold mb-4">Top Selling Courses</h3>

      {courses.map((c, i) => (
        <div key={i} className="flex justify-between py-2 border-b">
          <span>{c.name}</span>
          <span>{c.price}</span>
        </div>
      ))}
    </div>
  )
}

export default TopCourses