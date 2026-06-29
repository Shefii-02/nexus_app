import { useParams } from 'react-router-dom'
import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'

import {
  useAdmission,
  useUpdateAdmission,
} from '../teacherPaymentHooks'

import { admissionFormConfig } from '../admissionFormConfig'

const EditAdmissionPage = () => {

  const { id } = useParams()

  const { data } =
    useAdmission(
      Number(id)
    )

  const update =
    useUpdateAdmission()

  return (
    <div>
      <PageHeader
        title="Edit Admission"
      />

      <DynamicForm
        config={
          admissionFormConfig
        }

        defaultValues={
          data
        }

        onSubmit={(values) =>
          update.mutateAsync({
            id,
            data: values,
          })
        }
      />
    </div>
  )
}

export default EditAdmissionPage