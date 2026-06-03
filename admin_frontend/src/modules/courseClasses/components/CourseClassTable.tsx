
interface Props {
  data: any[]
  loading: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const CourseClassTable = ({
  data,
  loading,
  onView,
  onEdit,
  onDelete,
}: Props) => {
  return (
    <div className="border rounded-xl bg-white overflow-x-auto">
      <table className="w-full text-sm">
        <thead className="bg-gray-50">
          <tr>
            <th className="p-3 text-left">Title</th>
            <th className="p-3 text-left">Class No</th>
            <th className="p-3 text-left">Date</th>
            <th className="p-3 text-left">Status</th>
            <th className="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          {loading ? (
            <tr>
              <td colSpan={5} className="p-6 text-center">
                Loading...
              </td>
            </tr>
          ) : data.length === 0 ? (
            <tr>
              <td colSpan={5} className="p-6 text-center">
                No classes found
              </td>
            </tr>
          ) : (
            data.map((c) => (
              <tr key={c.id} className="border-t">
                <td className="p-3">{c.title}</td>
                <td className="p-3">{c.class_number}</td>
                <td className="p-3">{c.scheduled_date}</td>
                <td className="p-3">
                  <span className="px-2 py-1 rounded bg-gray-100">
                    {c.status}
                  </span>
                </td>

                <td className="p-3 text-right space-x-3">
                  <button onClick={() => onView(c.id)}>
                    View
                  </button>

                  <button onClick={() => onEdit(c.id)}>
                    Edit
                  </button>

                  <button
                    className="text-red-500"
                    onClick={() => onDelete(c.id)}
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

export default CourseClassTable