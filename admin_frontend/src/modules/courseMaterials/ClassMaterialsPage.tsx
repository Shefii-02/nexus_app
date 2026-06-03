import { useNavigate, useParams } from "react-router-dom"
import { useAppSelector } from "../../store/hooks"
import { useState } from "react"
import { useDeleteMaterial, useMaterials } from "./materialHooks"
import PageHeader from "../../components/PageHeader"
import Button from '../../components/Button'
import MaterialTable from "./MaterialTable"
import ConfirmModal from "../../components/ConfirmModal"
import { handleMutationWithToast } from "../../utils/handleMutationWithToast"


const CoursesMaterialsPage = () => {
  const navigate = useNavigate()

  const { courseId } = useParams()

  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  const [confirmId, setConfirmId] =
    useState<number | null>(null)

  const { data, isLoading } = useMaterials(
    Number(courseId),
    {
      page,
      search,
    }
  )

  const deleteMaterial = useDeleteMaterial()

  const handleView = (id: number) => {
    navigate(`/courses/${courseId}/materials/${id}`)
  }

  const handleEdit = (id: number) => {
    navigate(`/courses/${courseId}/materials/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Course Materials"
        subtitle="Manage Materials"
        actions={
          user?.acc_type === 'admin' && (
            <Button
              onClick={() =>
                navigate(
                  `/courses/${courseId}/materials/create`
                )
              }
            >
              + Create Material
            </Button>
          )
        }
      />

      <div className="bg-white p-4 rounded-xl shadow-sm">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search Material..."
          className="w-full md:w-80 border p-2 rounded"
        />
      </div>

      <MaterialTable
        data={data?.data || []}
        loading={isLoading}
        onView={handleView}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />

      <ConfirmModal
        open={confirmId !== null}
        title="Delete Material?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () =>
              deleteMaterial.mutateAsync({
                courseId: Number(courseId),
                id: confirmId as number,
              }),
            loadingMessage: 'Deleting Material...',
            successMessage: 'Material deleted successfully',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default CoursesMaterialsPage
