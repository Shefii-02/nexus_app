import { useNavigate } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { studentFormConfig } from '../studentFormConfig'
import { useCreateStudent } from '../studentHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import type { StudentFormPayload } from '../studentService'

const CreateStudentPage = () => {
  const navigate = useNavigate()
  const createStudent = useCreateStudent()

  const handleSubmit = async (data: StudentFormPayload) => {
    return handleMutationWithToast({
      action: () => createStudent.mutateAsync(data),
      loadingMessage: 'Creating student...',
      successMessage: 'Student created successfully',
      navigate,
      redirect: '/students',
    })
  }

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Create Student"
        subtitle="Add a new student to the system"
        onBack={() => navigate('/students')}
      />

      {/* FORM */}
      <DynamicForm
        config={studentFormConfig}
        defaultValues={{
          name: '',
          email: '',
          password: '',
          phone: '',
          roll_number: '',
          address: '',
          guardian_name: '',
          guardian_phone: '',
          status: 'active',
        }}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default CreateStudentPage