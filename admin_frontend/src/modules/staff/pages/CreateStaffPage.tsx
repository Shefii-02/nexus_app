import { useNavigate } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import type { StaffFormPayload } from '../staffService'
import { useCreateStaffMember } from '../staffHooks'
import { StaffFormConfig } from '../staffFormConfig'

const CreateStaffPage = () => {
  const navigate = useNavigate()
  const createStaff = useCreateStaffMember()

 const handleSubmit = async (data: StaffFormPayload) => {
  return handleMutationWithToast({
    action: () => createStaff.mutateAsync(data),
    loadingMessage: 'Creating staff...',
    successMessage: 'Staff created successfully',
    navigate,
    redirect: '/staff',
  })
}

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Create Staff"
        subtitle="Add a new staff member to the system"
        onBack={() => navigate('/staff')}
      />

      {/* FORM */}
      <DynamicForm
        config={StaffFormConfig}
        defaultValues={{
          name: '',
          email: '',
          password: '',
          phone: '',
          department: '',
          address: '',
          designation: '',
          status: 'active',
        }}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default CreateStaffPage