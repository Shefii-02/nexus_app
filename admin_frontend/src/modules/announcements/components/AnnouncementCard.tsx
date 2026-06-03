import Button from '../../../components/Button'

interface Props {
  item: any
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const AnnouncementCard = ({
  item,
  onView,
  onEdit,
  onDelete,
}: Props) => {
  return (
    <div className="bg-white rounded-xl border p-4 shadow-sm">
      <div className="flex justify-between">
        <h3 className="font-semibold">
          {item.title}
        </h3>

        <span
          className={`text-xs px-2 py-1 rounded-full ${
            item.status === 'active'
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700'
          }`}
        >
          {item.status}
        </span>
      </div>

      <p className="text-gray-500 mt-2 line-clamp-2">
        {item.content}
      </p>

      <div className="mt-4 text-sm text-gray-600">
        Target: {item.target_type}
      </div>

      <div className="mt-1 text-sm text-gray-600">
        Priority: {item.priority}
      </div>

      <div className="flex gap-2 mt-4">
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
    </div>
  )
}

export default AnnouncementCard