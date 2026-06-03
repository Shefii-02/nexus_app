import { useNavigate, useParams } from 'react-router-dom'
import { useStaffMember, useDeleteStaffMember } from '../staffHooks'
import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { useState } from 'react'

const StaffViewPage = () => {
  const { id } = useParams<{ id: string }>()
  const staffId = Number(id)

  const navigate = useNavigate()
  const { data, isLoading } = useStaffMember(staffId)
  const deleteStaff = useDeleteStaffMember()

  const [confirmOpen, setConfirmOpen] = useState(false)

  if (isLoading) {
    return <div className="p-6 bg-white rounded">Loading...</div>
  }

  if (!data) {
    return <div className="p-6 bg-white rounded">Staff not found</div>
  }

  return (
    <div className="space-y-6">

      {/* HEADER */}
      <PageHeader
        title="Staff Details"
        subtitle="View staff profile information"
        onBack={() => navigate('/staff')}
        actions={
          <div className="flex gap-2">
            <button
              onClick={() => navigate(`/staff/${staffId}/edit`)}
              className="px-4 py-2 bg-blue-600 text-white rounded"
            >
              Edit
            </button>

            <button
              onClick={() => setConfirmOpen(true)}
              className="px-4 py-2 bg-red-600 text-white rounded"
            >
              Delete
            </button>
          </div>
        }
      />

      {/* DETAILS CARD */}
      <div className="bg-white rounded-3xl border p-6 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <p className="text-sm text-gray-500">Name</p>
          <p className="font-medium">{data.user?.name || '-'}</p>
        </div>

        <div>
          <p className="text-sm text-gray-500">Email</p>
          <p className="font-medium">{data.user?.email || '-'}</p>
        </div>

        <div>
          <p className="text-sm text-gray-500">Phone</p>
          <p className="font-medium">{data.phone || '-'}</p>
        </div>

        <div>
          <p className="text-sm text-gray-500">Department</p>
          <p className="font-medium">{data.department || '-'}</p>
        </div>

        <div>
          <p className="text-sm text-gray-500">Designation</p>
          <p className="font-medium">{data.designation || '-'}</p>
        </div>

        <div>
          <p className="text-sm text-gray-500">Status</p>
          <span
            className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${
              data.status === 'active'
                ? 'bg-green-100 text-green-700'
                : 'bg-gray-100 text-gray-600'
            }`}
          >
            {data.status}
          </span>
        </div>

        <div className="md:col-span-2">
          <p className="text-sm text-gray-500">Address</p>
          <p className="font-medium">{data.address || '-'}</p>
        </div>

      </div>

      {/* DELETE CONFIRM */}
      <ConfirmModal
        open={confirmOpen}
        title="Delete Staff?"
        message="This action cannot be undone"
        confirmText="Delete"
        onCancel={() => setConfirmOpen(false)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteStaff.mutateAsync(staffId),
            loadingMessage: 'Deleting staff...',
            successMessage: 'Staff deleted successfully',
            navigate,
            redirect: '/staff',
          })
        }
      />
    </div>
  )
}

export default StaffViewPage