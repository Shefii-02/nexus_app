import { useNavigate, useParams } from 'react-router-dom'
import DynamicForm from '../../components/DynamicForm'
import PageHeader from '../../components/PageHeader'
import { handleMutationWithToast } from '../../utils/handleMutationWithToast'

import { useCreateMaterial } from './materialHooks'
import { materialFormConfig } from './materialFormConfig'

const CreateMaterial = () => {
  const navigate = useNavigate()
  const { courseId } = useParams()
  const CreateMaterial = useCreateMaterial(Number(courseId))

  const handleSubmit = async (data: any) => {
    const formData = new FormData()

    Object.keys(data).forEach((key) => {
      formData.append(key, data[key])
    })


    return handleMutationWithToast({
      action: () => CreateMaterial.mutateAsync(formData),
      loadingMessage: 'Creating Material...',
      successMessage: 'Material created successfully',
      navigate,
      redirect: `/courses/${courseId}/materials`,
    })
  }


  return (
    <div>
      <PageHeader title="Create Course Material" />
      <div className="pt-4">

        <DynamicForm
          config={materialFormConfig}
          defaultValues={{
            status: 'active',
          }}
          onSubmit={handleSubmit}
        />

      </div>
    </div>
  )
}

export default CreateMaterial