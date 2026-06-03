import { useNavigate } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { roleFormConfig } from '../roleFormConfig'
import { useCreateRole, usePermissions } from '../roleHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CreateRolePage = () => {
  const navigate = useNavigate()
  const createRole = useCreateRole()
  const { data: permissions } = usePermissions()

  const config = roleFormConfig.map((f) =>
    f.name === 'permissions'
      ? {
          ...f,
          options:
            permissions?.map((p) => ({
              label: p.name,
              value: p.name,
            })) || [],
        }
      : f
  )

  const handleSubmit = async (data: any) => {
    return handleMutationWithToast({
      action: () => createRole.mutateAsync(data),
      loadingMessage: 'Creating role...',
      successMessage: 'Role created',
      navigate,
      redirect: '/roles',
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader title="Create Role" onBack={() => navigate('/roles')} />

      <DynamicForm
        config={config}
        defaultValues={{ name: '', permissions: [] }}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default CreateRolePage