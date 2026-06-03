import { useNavigate, useParams } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { useCreateCourseClass } from '../courseClassHooks'
import { courseClassFormConfig } from '../courseClassFormConfig'
import { useTeachers } from '../../teachers/teacherHooks'

const CreateCourseClassPage = () => {
  const navigate = useNavigate()
   const { courseId } = useParams()
  const createClass = useCreateCourseClass(Number(courseId))

  const handleSubmit = async (data: any) => {
    return handleMutationWithToast({
      action: () => createClass.mutateAsync(data),
      loadingMessage: 'Creating class...',
      successMessage: 'Class created successfully',
      navigate,
      redirect: `/courses/${courseId}/classes`,
    })
  }

  // ✅ FETCH TEACHERS HERE (NOT in config file)
  const { data: teacherData } = useTeachers()

  // ✅ INJECT options dynamically
  const config = courseClassFormConfig.map((field) =>
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


  return (
    <div>
      <PageHeader title="Create Course Class" />
      <div className="pt-4">

        <DynamicForm
          config={config}
          defaultValues={{
            status: 'scheduled',
          }}
          onSubmit={handleSubmit}
        />

      </div>
    </div>
  )
}

export default CreateCourseClassPage