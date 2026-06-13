import { useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useCourse, useUpdateCourse } from '../courseHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import { courseFormConfig } from '../courseFormConfig'
import DynamicForm from '../../../components/DynamicForm'
import { useTeachers } from '../../teachers/teacherHooks'

const EditCoursePage = () => {
  const { id } = useParams()
  const courseId = Number(id)

  const navigate = useNavigate()

  /** =========================
   * FETCH DATA
   ========================= */
  const { data, isLoading,refetch } = useCourse(courseId)
  const { data: teacherData } = useTeachers()
  const updateCourse = useUpdateCourse()

  /** =========================
   * DYNAMIC CONFIG
   ========================= */
  const config = courseFormConfig.map((field) =>
    field.name === 'teacher_id'
      ? {
          ...field,
          options:
            teacherData?.data.map((t) => ({
              label: t.user.name,
              value: t.user.id, // ✅ FIXED
            })) || [],
        }
      : field
  )

  /** =========================
   * FORMAT DATE
   ========================= */
  const formatDate = (date?: string) => {
    if (!date) return ''
    return date.split('T')[0] // handles both date & datetime
  }

  /** =========================
   * DEFAULT VALUES
   ========================= */
  const defaultValues = data
    ? {
        code: data.code || '',
        name: data.name || '',
        description: data.description || '',

        actual_price: data.actual_price || '',
        net_price: data.net_price || '',

        teacher_id: data.teacher_id || '',
        is_renewal: data.is_renewal ? 1 : 0,

        fee_type: data.fee_type || '',
        class_type: data.class_type || '',

        duration_days: data.duration_days || '',
        status: data.status || 'active',

        started_at: formatDate(data.started_at),
        ended_at: formatDate(data.ended_at),

        thumbnail: data.thumbnail || '',
      }
    : {}

  /** =========================
   * SUBMIT
   ========================= */
  const handleSubmit = async (form: any) => {
    const formData = new FormData()

    Object.keys(form).forEach((key) => {
      const value = form[key]

      if (value === null || value === undefined) return

      /** 🔥 skip old image */
      if (key === 'thumbnail' && typeof value === 'string') {
        return
      }

      formData.append(key, value)
    })

    return handleMutationWithToast({
      action: () =>
        updateCourse.mutateAsync({
          id: courseId,
          payload: formData,
        }),
      loadingMessage: 'Updating course...',
      successMessage: 'Course updated',
      navigate,
      redirect: '/courses',
    })
  }

  /** =========================
   * LOADING
   ========================= */
  if (isLoading) {
    return <div className="p-6">Loading course...</div>
  }

  return (
    <DynamicForm
      key={courseId} // 🔥 VERY IMPORTANT (forces re-render)
      config={config}
      defaultValues={defaultValues}
      onSubmit={handleSubmit}
      isEdit
    />
  )
}

export default EditCoursePage