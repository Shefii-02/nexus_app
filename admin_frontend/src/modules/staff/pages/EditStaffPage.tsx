import { useNavigate, useParams } from 'react-router-dom'
import { useStaffMember, useUpdateStaffMember } from '../staffHooks'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { StaffFormConfig } from '../staffFormConfig'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const EditStaffPage = () => {
  const { id } = useParams<{ id: string }>()
  const staffId = Number(id)

  const navigate = useNavigate()
  const { data, isLoading } = useStaffMember(staffId)
  const updateStaff = useUpdateStaffMember()

  const defaultValues = data
    ? {
        name: data.user?.name || '',
        email: data.user?.email || '',
        phone: data.phone || '',
        department: data.department || '',
        designation: data.designation || '',
        address: data.address || '',
        status: data.status || 'active',
      }
    : undefined

  const handleSubmit = async (formData: any) => {
    return handleMutationWithToast({
      action: () =>
        updateStaff.mutateAsync({
          id: staffId,
          payload: formData,
        }),
      loadingMessage: 'Updating staff...',
      successMessage: 'Staff updated successfully',
      navigate,
      redirect: '/staff',
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Edit Staff"
        subtitle="Update staff details"
        onBack={() => navigate('/staff')}
      />

      {isLoading || !defaultValues ? (
        <div className="p-6 bg-white rounded">Loading...</div>
      ) : (
        <DynamicForm
          config={StaffFormConfig}
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          isEdit
        />
      )}
    </div>
  )
}

export default EditStaffPage