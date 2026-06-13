import { useNavigate, useParams } from 'react-router-dom'
import { useCourseClasses, useDeleteCourseClass } from '../courseClassHooks'
import PageHeader from '../../../components/PageHeader'
import Button from '../../../components/Button'
import CourseClassTable from '../components/CourseClassTable'
import { useState } from 'react'
import { useAppSelector } from '../../../store/hooks'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CourseClassListPage = () => {
  const navigate = useNavigate()

  const { courseId } = useParams()

  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  const [confirmId, setConfirmId] =
    useState<number | null>(null)

    const { data, isLoading } = useCourseClasses(
  Number(courseId),
  {
    page,
    search,
  }
)

  const deleteClass = useDeleteCourseClass()

  const handleView = (id: number) => {
    navigate(`/courses/${courseId}/classes/${id}/show`)
  }

  const handleEdit = (id: number) => {
    navigate(`/courses/${courseId}/classes/${id}`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Course Classes"
        subtitle="Manage Classes"
        actions={
          user?.acc_type === 'admin' && (
            <Button
              onClick={() =>
                navigate(
                  `/courses/${courseId}/classes/create`
                )
              }
            >
              + Create Class
            </Button>
          )
        }
      />

      <div className="bg-white p-4 rounded-xl shadow-sm">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search class..."
          className="w-full md:w-80 border p-2 rounded"
        />
      </div>

      <CourseClassTable
        data={data?.data || []}
        loading={isLoading}
        onView={handleView}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />

      <ConfirmModal
        open={confirmId !== null}
        title="Delete Class?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () =>
              deleteClass.mutateAsync(
                confirmId as number
              ),
            loadingMessage: 'Deleting class...',
            successMessage:
              'Class deleted successfully',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default CourseClassListPage
