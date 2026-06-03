import { useNavigate, useParams } from 'react-router-dom'
import { useTeacher, useUpdateTeacher } from '../teacherHooks'
import DynamicForm from '../../../components/DynamicForm'
import { teacherFormConfig } from '../teacherFormConfig'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const EditTeacherPage = () => {
  const { id } = useParams<{ id: string }>()
  const teacherId = Number(id)
  const navigate = useNavigate()

  const { data, isLoading } = useTeacher(teacherId)
  const updateTeacher = useUpdateTeacher()

  // ✅ Transform API → form
  const defaultValues = data
    ? {
        name: data.user?.name || '',
        email: data.user?.email || '',
        phone: data.user?.phone || '',
        subject: data.subject || '',
        qualification: data.qualification || '',
        experience_years: data.experience_years || 0,
        address: data.address || '',
        status: data.user?.status || 'active',
        password: '', // always empty
      }
    : undefined

  const handleSubmit = async (formData: any) => {
    // ✅ remove empty password
    if (!formData.password) {
      delete formData.password
    }

    await handleMutationWithToast({
      action: () =>
        updateTeacher.mutateAsync({
          id: teacherId,
          payload: formData,
        }),
      loadingMessage: 'Updating teacher...',
      successMessage: 'Teacher updated successfully',
      redirect: '/teachers',
      navigate,
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Edit Teacher"
        subtitle="Update teacher profile"
        onBack={() => navigate('/teachers')}
      />

      {isLoading || !defaultValues ? (
        <div className="p-6 bg-white rounded shadow">
          Loading...
        </div>
      ) : (
        <DynamicForm
          config={teacherFormConfig}
          defaultValues={defaultValues}
          onSubmit={handleSubmit}
          isEdit
        />
      )}
    </div>
  )
}

export default EditTeacherPage