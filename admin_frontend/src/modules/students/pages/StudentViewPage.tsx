import { useNavigate, useParams } from 'react-router-dom'
import { useStudent, useDeleteStudent } from '../studentHooks'
import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'
import { useState } from 'react'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import Button from '../../../components/Button'

const StudentViewPage = () => {
  const { id } = useParams<{ id: string }>()
  const studentId = Number(id)

  const navigate = useNavigate()
  const { data, isLoading } = useStudent(studentId)
  const deleteStudent = useDeleteStudent()

  const [confirmOpen, setConfirmOpen] = useState(false)

  if (isLoading) {
    return <div className="p-6 bg-white rounded shadow">Loading...</div>
  }

  if (!data) {
    return <div className="p-6 bg-white rounded shadow">Student not found</div>
  }

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Student Details"
        subtitle="View student profile information"
        onBack={() => navigate('/students')}
        actions={
          <>
            <Button
              variant="secondary"
              onClick={() => navigate(`/teachers/edit/${id}`)}
            >
              Edit
            </Button>

            <Button
              variant="danger"
              onClick={() => setConfirmOpen(true)}
            >
              Delete
            </Button>
          </>
        }

      />

      {/* USER INFO */}
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-lg font-semibold mb-4">User Information</h3>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <p><b>Name:</b> {data.user?.name || '-'}</p>
          <p><b>Email:</b> {data.user?.email || '-'}</p>
          <p><b>Phone:</b> {data.phone || '-'}</p>
          <p><b>Status:</b> {data.status || '-'}</p>
        </div>
      </div>

      {/* STUDENT INFO */}
      <div className="bg-white p-6 rounded-xl shadow-sm border">
        <h3 className="text-lg font-semibold mb-4">Student Information</h3>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <p><b>Roll Number:</b> {data.roll_number}</p>
          <p><b>Guardian Name:</b> {data.guardian_name}</p>
          <p><b>Guardian Phone:</b> {data.guardian_phone}</p>
        </div>

        <div className="mt-4">
          <p><b>Address:</b></p>
          <p className="text-gray-600">{data.address || '-'}</p>
        </div>
      </div>

      {/* DELETE CONFIRM */}
      <ConfirmModal
        open={confirmOpen}
        title="Delete Student?"
        message="This action cannot be undone"
        confirmText="Delete"
        onCancel={() => setConfirmOpen(false)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteStudent.mutateAsync(studentId),
            successMessage: 'Student deleted successfully',
            navigate,
            redirect: '/students',
          })
        }
      />
    </div>
  )
}

export default StudentViewPage