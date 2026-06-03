import { useNavigate } from 'react-router-dom'
import { useCreateCourse } from '../courseHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { courseFormConfig } from '../courseFormConfig'
import { useTeachers } from '../../teachers/teacherHooks'

const CreateCoursePage = () => {
  const navigate = useNavigate()
  const createCourse = useCreateCourse()

  // ✅ FETCH TEACHERS HERE (NOT in config file)
  const { data: teacherData } = useTeachers()

  // ✅ INJECT options dynamically
  const config = courseFormConfig.map((field) =>
    field.name === 'teacher_id'
      ? {
          ...field,
          options:
            teacherData?.data.map((t) => ({
              label: t.user.name,
              value: t.user.id,
            })) || [],
        }
      : field
  )

  const handleSubmit = async (data: any) => {
    const formData = new FormData()

    Object.keys(data).forEach((key) => {
      formData.append(key, data[key])
    })

    return handleMutationWithToast({
      action: () => createCourse.mutateAsync(formData),
      loadingMessage: 'Creating course...',
      successMessage: 'Course created',
      navigate,
      redirect: '/courses',
    })
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Create Course"
        subtitle="Add a new course"
        onBack={() => navigate('/courses')}
      />

      <DynamicForm
        config={config}   // ✅ use dynamic config
        defaultValues={{}}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default CreateCoursePage