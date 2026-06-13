import ActionDropdown from '../../../components/ActionButtonDropdown'
import { useNavigate } from 'react-router-dom'

interface Props {
  course: any
  onDelete: (id: number) => void
}

const CourseListItem = ({ course, onDelete }: Props) => {
  const navigate = useNavigate()

  const actions = [
    {
      label: 'View',
      onClick: () => navigate(`/courses/${course.id}`),
    },
    {
      label: 'Classes',
      onClick: () => navigate(`/courses/${course.id}/classes`),
    },
    {
      label: 'Materials',
      onClick: () => navigate(`/courses/${course.id}/materials`),
    },
    {
      label: 'Edit',
      onClick: () => navigate(`/courses/${course.id}/edit`),
    },
    {
      label: 'Delete',
      onClick: () => onDelete(course.id),
      danger: true,
    },
  ]

  return (<div className="flex justify-between items-center border p-4 rounded-xl bg-white"> <div> <h2 className="font-semibold">{course.name}</h2>


    <p className="text-sm text-gray-500">
      {course.code}
    </p>

    <div className="flex gap-2 mt-2">
      <span className="text-xs px-2 py-1 bg-gray-100 rounded">
        {course.mode}
      </span>

      <span className="text-xs px-2 py-1 bg-gray-100 rounded">
        {course.status}
      </span>
    </div>
  </div>

    <div className="flex items-center gap-4">
      <span className="font-medium">
        ₹{course.net_price}
      </span>

      <ActionDropdown actions={actions} />
    </div>
  </div>


  )
}

export default CourseListItem
