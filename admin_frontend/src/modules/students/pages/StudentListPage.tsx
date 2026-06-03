import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import StudentTable from '../components/StudentTable'
import { useDeleteStudent, useStudents } from '../studentHooks'
import { useAppSelector } from '../../../store/hooks'
import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const StudentListPage = () => {
  const navigate = useNavigate()
  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading, error } = useStudents({ page, search, status })
  const deleteStudent = useDeleteStudent()

  const students = (data?.data || []).filter((s) => {

    const matchesSearch =
      s.user?.name?.toLowerCase().includes(search.toLowerCase()) ||
      s.user?.email?.toLowerCase().includes(search.toLowerCase())

    const matchesStatus =
      !status ||
      s.status === status ||
      s.user?.status === status

    return matchesSearch && matchesStatus
  })


  const handleEdit = (id: number) => {
    navigate(`/students/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setConfirmId(id)
  }

  return (
    <div className="space-y-6">

      {/* HEADER */}
      <PageHeader
        title="Students"
        subtitle="Manage student registrations and contact details"
        actions={
          user?.acc_type === 'admin' && (
            <button
              onClick={() => navigate('/students/create')}
              className="bg-black text-white px-4 py-2 rounded"
            >
              + Create Student
            </button>
          )
        }
      />

      <div className="flex gap-3">
        {/* SEARCH */}
        <div className="bg-white p-4 rounded-xl shadow-sm">
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search students..."
            className="w-full md:w-80 border p-2 rounded"
          />
        </div>

        <div className="bg-white p-4 rounded-xl shadow-sm">

          <select
            value={status}
            onChange={(e) => setStatus(e.target.value)}
            className="w-full md:w-80 border p-2 rounded"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>





      {/* ERROR */}
      {
        error && (
          <div className="bg-red-50 text-red-600 p-4 rounded">
            Failed to load students
          </div>
        )
      }

      {/* TABLE */}
      <StudentTable
        students={students}
        loading={isLoading}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onView={(id) => navigate(`/students/${id}`)}
      />

      {/* DELETE CONFIRM */}
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Student?"
        message="This action cannot be undone"
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          confirmId &&
          handleMutationWithToast({
            action: () => deleteStudent.mutateAsync(confirmId),
            successMessage: 'Student deleted successfully',
            navigate,
            redirect: '/students',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div >
  )
}

export default StudentListPage