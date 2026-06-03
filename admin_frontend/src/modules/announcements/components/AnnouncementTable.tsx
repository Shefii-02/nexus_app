import Button from '../../../components/Button'

interface Props {
  data: any[]
  loading?: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const AnnouncementTable = ({
  data,
  loading,
  onView,
  onEdit,
  onDelete,
}: Props) => {
  if (loading) {
    return (
      <div className="bg-white rounded-xl p-6">
        Loading...
      </div>
    )
  }

  return (
    <div className="overflow-x-auto bg-white rounded-xl shadow-sm border">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-gray-50">
            <th className="p-3 text-left">Title</th>
            <th className="p-3 text-left">Target</th>
            <th className="p-3 text-left">Priority</th>
            <th className="p-3 text-left">Status</th>
            <th className="p-3 text-left">Start</th>
            <th className="p-3 text-left">End</th>
            <th className="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          {data.map((item) => (
            <tr
              key={item.id}
              className="border-t hover:bg-gray-50"
            >
              <td className="p-3 font-medium">
                {item.title}
              </td>

              <td className="p-3">
                {item.target_type}
              </td>

              <td className="p-3">
                {item.priority}
              </td>

              <td className="p-3">
                <span
                  className={`px-2 py-1 rounded-full text-xs ${
                    item.status === 'active'
                      ? 'bg-green-100 text-green-700'
                      : 'bg-red-100 text-red-700'
                  }`}
                >
                  {item.status}
                </span>
              </td>

              <td className="p-3">
                {item.start_date}
              </td>

              <td className="p-3">
                {item.end_date}
              </td>

              <td className="p-3 text-right">
                <div className="flex gap-2 justify-end">
                  <Button
                    size="sm"
                    onClick={() =>
                      onView(item.id)
                    }
                  >
                    View
                  </Button>

                  <Button
                    size="sm"
                    variant="secondary"
                    onClick={() =>
                      onEdit(item.id)
                    }
                  >
                    Edit
                  </Button>

                  <Button
                    size="sm"
                    variant="danger"
                    onClick={() =>
                      onDelete(item.id)
                    }
                  >
                    Delete
                  </Button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default AnnouncementTable