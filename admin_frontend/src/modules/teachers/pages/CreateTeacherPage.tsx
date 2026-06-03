

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { teacherFormConfig } from '../teacherFormConfig'
import { useCreateTeacher } from '../teacherHooks'
import type { TeacherFormPayload } from '../teacherService'
import { useNavigate } from 'react-router-dom'
import { toast } from 'react-toastify'

const CreateTeacherPage = () => {
  const navigate = useNavigate()
  const createTeacher = useCreateTeacher()

  const handleSubmit = async (data: TeacherFormPayload) => {
    await handleMutationWithToast({
      action: () => createTeacher.mutateAsync(data),
      loadingMessage: 'Creating teacher...',
      successMessage: 'Teacher created successfully',
      redirect: '/teachers',
      navigate,
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Create Teacher"
        subtitle="Add a new teacher"
        onBack={() => navigate('/teachers')}
      />

      <DynamicForm
        config={teacherFormConfig}
        defaultValues={{
          name: '',
          email: '',
          phone: '',
          password: '',
          subject: '',
          qualification: '',
          experience_years: 0,
          address: '',
          status: 'active',
        }}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default CreateTeacherPage