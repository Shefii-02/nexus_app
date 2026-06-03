import { useNavigate, useParams } from 'react-router-dom'
import { useStudent, useUpdateStudent } from '../studentHooks'
import DynamicForm from '../../../components/DynamicForm'
import { studentFormConfig } from '../studentFormConfig'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import PageHeader from '../../../components/PageHeader'

const EditStudentPage = () => {
  const { id } = useParams<{ id: string }>()
  const studentId = Number(id)

  const navigate = useNavigate()

  const { data, isLoading } = useStudent(studentId)
  const updateStudent = useUpdateStudent()

  // ✅ Transform API → form values
  const defaultValues = data
    ? {
        name: data.user?.name || '',
        email: data.user?.email || '',
        phone: data.phone || '',
        roll_number: data.roll_number || '',
        address: data.address || '',
        guardian_name: data.guardian_name || '',
        guardian_phone: data.guardian_phone || '',
        status: data.status || 'active',
        password: '', // always empty
      }
    : undefined

  const handleSubmit = async (formData: any) => {
    // ✅ remove empty password
    if (!formData.password) {
      delete formData.password
    }

    return handleMutationWithToast({
      action: () =>
        updateStudent.mutateAsync({
          id: studentId,
          payload: formData,
        }),
      loadingMessage: 'Updating student...',
      successMessage: 'Student updated successfully',
      navigate,
      redirect: '/students',
    })
  }

  return (
    <div className="space-y-6">
      {/* HEADER */}
      <PageHeader
        title="Edit Student"
        subtitle="Update student details"
        onBack={() => navigate('/students')}
      />

      {/* FORM */}
      {isLoading || !defaultValues ? (
        <div className="p-6 bg-white rounded shadow">Loading...</div>
      ) : (
        <DynamicForm
          config={studentFormConfig}
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          isEdit
        />
      )}
    </div>
  )
}

export default EditStudentPage