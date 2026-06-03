import { useParams, useNavigate } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { useCourseClass, useUpdateCourseClass } from '../courseClassHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { courseClassFormConfig } from '../courseClassFormConfig'
import { useEffect } from 'react'
import { useFormContext } from 'react-hook-form'
import { useTeachers } from '../../teachers/teacherHooks'

const EditCourseClassPage = () => {

  const navigate = useNavigate()
  const { courseId, classId } = useParams()

  const { data } = useCourseClass(Number(classId), Number(courseId))
  const updateClass = useUpdateCourseClass()

  const handleSubmit = async (formData: any) => {

    return handleMutationWithToast({
      action: () =>
        updateClass.mutateAsync({
          id: Number(classId),
          data: formData,
          courseId: Number(courseId)
        }),
      loadingMessage: 'Updating class...',
      successMessage: 'Class updated',
      navigate,
      redirect: `/courses/${courseId}/classes`,
    })
  }

  const { data: teacherData } = useTeachers()

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


  const defaultValues = data
    ? {
      ...data,
      teacher_id: String(data.teacher_id ?? ''),
      source: String(data.source ?? ''),
      status: String(data.status ?? ''),
      scheduled_date: data.scheduled_date
        ? data.scheduled_date.split(' ')[0]
        : '',
      started_at: data.started_at
        ? data.started_at
        : '',
      ended_at: data.ended_at
        ? data.ended_at
        : '',
    }
    : {}


  return (
    <div>
      <PageHeader title="Edit Course Class" />

      <DynamicForm
        config={config}
        defaultValues={defaultValues}
        onSubmit={handleSubmit}
        isEdit
      />
    </div>
  )
}

export default EditCourseClassPage