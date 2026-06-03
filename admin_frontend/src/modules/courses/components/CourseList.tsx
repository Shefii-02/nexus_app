// components/CourseList.tsx
const CourseList = ({ courses, onAction }: any) => {
  return (
    <div className="space-y-3">
      {courses.map((c: any) => (
        <div
          key={c.id}
          className="flex justify-between items-center p-4 bg-white rounded-xl shadow"
        >
          <div>
            <h3>{c.name}</h3>
            <p className="text-sm text-gray-500">{c.code}</p>
          </div>

          <div>₹{c.net_price}</div>

          <div className="flex gap-2">
            <button onClick={() => onAction('view', c.id)}>View</button>
            <button onClick={() => onAction('edit', c.id)}>Edit</button>
            <button onClick={() => onAction('delete', c.id)}>Delete</button>
          </div>
        </div>
      ))}
    </div>
  )
}

export default CourseList