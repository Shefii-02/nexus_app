const CourseListItem = ({ course, onDelete }: any) => {
  return (
    <div className="flex justify-between border p-4 rounded-xl">
      <div>
        <h2 className="font-semibold">{course.name}</h2>
        <p className="text-sm text-gray-500">{course.code}</p>
      </div>

      <div className="flex items-center gap-4">
        <span>₹{course.net_price}</span>

        <button
          onClick={() => onDelete(course.id)}
          className="text-red-500"
        >
          Delete
        </button>
      </div>
    </div>
  )
}

export default CourseListItem