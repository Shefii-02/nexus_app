import { useState } from 'react'
import type { useRoles, Role } from '../roleService'
import ConfirmModal from '../../../components/ConfirmModal'
import Dropdown from '../../../components/Dropdown'
import { MoreVertical } from 'lucide-react'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { useDeleteRole } from '../roleHooks'
import { useNavigate } from 'react-router-dom'

interface RoleTableProps {
  roles: Role[]
  loading: boolean
  onEdit: (id: number) => void
  onView?: (id: number) => void
}

const RoleTable = ({ roles, loading, onEdit, onView, onDelete }: RoleTableProps) => {
  const [confirmId, setConfirmId] = useState<number | null>(null)

  const deleteRole = useDeleteRole()
  const navigate = useNavigate()

  return (
    <>
      <div className="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table className="min-w-full divide-y divide-slate-200 text-sm">
          <thead className="bg-slate-50">
            <tr>
              <th className="px-6 py-4 text-left">Name</th>
              <th className="px-6 py-4 text-left">Permissions</th>
              <th className="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td colSpan={5} className="p-6 text-center">Loading...</td>
              </tr>
            ) : roles.length === 0 ? (
              <tr>
                <td colSpan={5} className="p-6 text-center">No data</td>
              </tr>
            ) : (
              roles.map((role) => (
                <tr key={role.id}>
                  <td className="px-6 py-4 capitalize">{role.name ?? '—'}</td>
                  <td className="px-6 py-4">{role.permissions.length}</td>

                  <td className="px-6 py-4 text-right">
                    <Dropdown
                      trigger={
                        <button className="p-2 rounded hover:bg-gray-100">
                          <MoreVertical size={18} />
                        </button>
                      }
                      items={[
                        {
                          label: 'View',
                          onClick: () => onView?.(role.id),
                        },
                        {
                          label: 'Edit',
                          onClick: () => onEdit(role.id),
                        },
                        ...(role.name !== 'admin' && role.name !== 'teacher' && role.name !== 'student'
                          ? [
                              {
                                label: 'Delete',
                                danger: true,
                                onClick: () => setConfirmId(role.id),
                              }
                            ]
                          : []),

                      ]}
                    />
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {/* ✅ Confirm Modal */}
      <ConfirmModal
        open={confirmId !== null}
        title="Delete Role?"
        message="This cannot be undone."
        confirmText="Delete"
        onCancel={() => setConfirmId(null)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteRole.mutateAsync(confirmId as number),
            loadingMessage: 'Deleting role...',
            successMessage: 'Role deleted successfully',
            onSuccess: () => setConfirmId(null), // no redirect needed
          })
        }
      />
    </>
  )
}

export default RoleTable