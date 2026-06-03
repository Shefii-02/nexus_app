import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAppSelector } from '../../../store/hooks'
import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

import StaffTable from '../components/StaffTable'
import { useDeleteStaffMember, useStaffMembers } from '../staffHooks'

const StaffListPage = () => {
  const navigate = useNavigate()
  const user = useAppSelector((state) => state.auth.user)

  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('')

  const [page, setPage] = useState(1)
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const { data, isLoading, error } = useStaffMembers({ page, search, status })
  const deleteStaff = useDeleteStaffMember()

  const filteredStaff = (data?.data || []).filter((s) => {

    const matchesSearch =
      s.user?.name?.toLowerCase().includes(search.toLowerCase()) ||
      s.user?.email?.toLowerCase().includes(search.toLowerCase())

    const matchesStatus =
      !status ||
      s.status === status ||
      s.user?.status === status

    return matchesSearch && matchesStatus
  })

  return (
    <div className="space-y-6">

      {/* HEADER */}
      <PageHeader
        title="Staff"
        subtitle="Manage staff members"
        onBack={() => navigate('/')}
        actions={
          user?.acc_type === 'admin' && (
            <button
              onClick={() => navigate('/staff/create')}
              className="bg-black text-white px-4 py-2 rounded"
            >
              + Create Staff
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
            placeholder="Search staff..."
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
      {error && (
        <div className="bg-red-50 text-red-600 p-4 rounded">
          Failed to load staff
        </div>
      )}

      {/* TABLE */}
      <StaffTable
        staff={filteredStaff}
        loading={isLoading}
        onEdit={(id) => navigate(`/staff/${id}/edit`)}
        onView={(id) => navigate(`/staff/${id}`)}
        onDelete={(id) => setConfirmId(id)}
      />



      {/* DELETE CONFIRM */}
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Staff?"
        message="This action cannot be undone"
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          confirmId &&
          handleMutationWithToast({
            action: () => deleteStaff.mutateAsync(confirmId),
            loadingMessage: 'Deleting staff...',
            successMessage: 'Staff deleted successfully',
            onSuccess: () => setConfirmId(null),
          })
        }
      />
    </div>
  )
}

export default StaffListPage