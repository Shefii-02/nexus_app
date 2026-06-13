import { useState } from 'react'
import ActionDropdown from '../../../components/ActionButtonDropdown'
import { useNavigate } from 'react-router-dom'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { useDeleteCourse } from '../courseHooks'
import CourseTeacherDrawer from './CourseTeacherDrawer'

const CourseCard = ({ course, onDelete, onTeachers, onStudents,onConversation }: any) => {
  const navigate = useNavigate()
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const deleteCourse = useDeleteCourse()
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
      label: 'Addon Teachers',
      onClick: () => onTeachers(course),
    },

    {
      label: 'Entrolled Students',
      onClick: () => onStudents(course),
    },

    {
      label: 'Conversation Group',
      onClick: () => {
        onConversation(course)
      }
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
  // console.log(course)
  return (
    <>
      <div className="border rounded-xl p-4 hover:shadow relative">
        <div className="absolute top-3 right-5">
          <ActionDropdown actions={actions} />
        </div>

        <img
          src={course.thumbnail}
          className="h-40 w-full object-cover rounded"
        />

        <h2 className="mt-3 font-semibold">{course.name}</h2>
        <p className="text-sm text-gray-500">{course.code}</p>

        <div className="flex justify-between mt-3">
          <span>₹{course.net_price}</span>
          <span className="text-xs">{course.status}</span>
        </div>
      </div>
      {/* ✅ Confirm Modal */}
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Teacher?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteCourse.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting course...',
            successMessage: 'Course deleted successfully',
            onSuccess: () => setConfirmId(null), // no redirect needed
          })
        }
      />
    </>
  )
}

export default CourseCard