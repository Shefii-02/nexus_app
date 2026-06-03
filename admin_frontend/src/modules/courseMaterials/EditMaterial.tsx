import { useParams, useNavigate } from 'react-router-dom'
import DynamicForm from '../../components/DynamicForm'
import PageHeader from '../../components/PageHeader'
import { handleMutationWithToast } from '../../utils/handleMutationWithToast'
import { useMaterial, useUpdateMaterial } from './materialHooks'
import { materialFormConfig } from './materialFormConfig'

const EditMaterial = () => {

  const navigate = useNavigate()
  const { courseId, materialId } = useParams()

  const { data } = useMaterial(Number(materialId), Number(courseId))
  const updateMaterial = useUpdateMaterial()

  /** =========================
   * SUBMIT
   ========================= */
  const handleSubmit = async (
    form: any
  ) => {
    const formData = new FormData()

    Object.entries(form).forEach(
      ([key, value]) => {
        if (
          value === null ||
          value === undefined
        )
          return

        if (
          key === 'file_url' &&
          typeof value === 'string'
        )
          return

        formData.append(
          key,
          value as any
        )
      }
    )

    return handleMutationWithToast({
      action: () =>
        updateMaterial.mutateAsync({
          courseId: Number(courseId),
          id: Number(materialId),
          payload: formData,
        }),

      loadingMessage:
        'Updating Material...',

      successMessage:
        'Material updated',

      navigate,

      redirect: `/courses/${courseId}/materials`,
    })
  }


  const defaultValues = {
    ...data,

    file_url:
      data?.file_url || '',
  }



  return (
    <div>
      <PageHeader title="Edit Course Material" />

      <DynamicForm
        config={materialFormConfig}
        defaultValues={defaultValues}
        onSubmit={handleSubmit}
        isEdit
      />
    </div>
  )
}

export default EditMaterial