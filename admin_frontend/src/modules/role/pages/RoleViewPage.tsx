import { useNavigate, useParams } from 'react-router-dom'
import { useRole, useDeleteRole } from '../roleHooks'
import PageHeader from '../../../components/PageHeader'
import ConfirmModal from '../../../components/ConfirmModal'
import { useState } from 'react'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const RoleViewPage = () => {
  const { id } = useParams()
  const roleId = Number(id)

  const navigate = useNavigate()
  const { data } = useRole(roleId)
  const deleteRole = useDeleteRole()

  const [confirmOpen, setConfirmOpen] = useState(false)

  if (!data) return <div>Loading...</div>

  return (
    <div className="space-y-6">
      <PageHeader
        title={data.name}
        onBack={() => navigate('/roles')}
        actions={
          <>
            <button onClick={() => navigate(`/roles/${roleId}/edit`)}>
              Edit
            </button>
            <button onClick={() => setConfirmOpen(true)}>Delete</button>
          </>
        }
      />

      <div className="bg-white p-6 rounded">
        <h3 className="font-semibold mb-2">Permissions</h3>

        <div className="grid grid-cols-2 gap-2">
          {data.permissions.map((p) => (
            <div key={p.id} className="text-sm bg-gray-100 px-2 py-1 rounded">
              {p.label}
            </div>
          ))}
        </div>
      </div>

      <ConfirmModal
        open={confirmOpen}
        title="Delete Role?"
        message="This cannot be undone"
        confirmText="Delete"
        onCancel={() => setConfirmOpen(false)}
        onConfirm={() =>
          handleMutationWithToast({
            action: () => deleteRole.mutateAsync(roleId),
            successMessage: 'Role deleted',
            navigate,
            redirect: '/roles',
          })
        }
      />
    </div>
  )
}

export default RoleViewPage