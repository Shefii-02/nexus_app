import { useNavigate, useParams } from 'react-router-dom'
import { useRole, useUpdateRole, usePermissions } from '../roleHooks'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { roleFormConfig } from '../roleFormConfig'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const EditRolePage = () => {
  const { id } = useParams()
  const roleId = Number(id)

  const navigate = useNavigate()

  const { data } = useRole(roleId)
  const { data: permissions } = usePermissions()
  const updateRole = useUpdateRole()

  if (!data || !permissions) return <div>Loading...</div>

  const config = roleFormConfig.map((f) =>
    f.name === 'permissions'
      ? {
          ...f,
          options: permissions.map((p) => ({
            label: p.label,
            value: p.name,
          })),
        }
      : f
  )

  const defaultValues = {
    name: data.name,
    permissions: data.permissions.map((p) => p.name), // 🔥 auto checked
  }

  const handleSubmit = async (formData: any) => {
    return handleMutationWithToast({
      action: () =>
        updateRole.mutateAsync({
          id: roleId,
          payload: formData,
        }),
      loadingMessage: 'Updating role...',
      successMessage: 'Role updated',
      navigate,
      redirect: '/roles',
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader title="Edit Role" onBack={() => navigate('/roles')} />

      <DynamicForm
        config={config}
        defaultValues={defaultValues}
        onSubmit={handleSubmit}
        isEdit
      />
    </div>
  )
}

export default EditRolePage