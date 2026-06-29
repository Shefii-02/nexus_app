import { useNavigate } from 'react-router-dom'

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'

import { admissionFormConfig } from '../admissionFormConfig'

import { useCreateAdmission } from '../staffPaymentHooks'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CreateAdmissionPage = () => {

  const navigate =
    useNavigate()

  const createAdmission =
    useCreateAdmission()

  const handleSubmit = async (data: any) => {

    return handleMutationWithToast({

      action: () =>
        createAdmission.mutateAsync({
          ...data,

          actual_fee:
            Number(data.actual_fee),

          discount_amount:
            Number(
              data.discount_amount || 0
            ),

          net_fee:
            Number(data.net_fee),

          paid_amount:
            Number(
              data.paid_amount || 0
            ),
        }),

      loadingMessage:
        'Creating admission...',

      successMessage:
        'Admission created successfully',

      navigate,

      redirect:
        '/admissions',
    })
  }

  return (
    <div>
      <PageHeader
        title="Create Admission"
      />

      <div className="pt-4">
        <DynamicForm
          config={
            admissionFormConfig
          }
          onSubmit={
            handleSubmit
          }
        />
      </div>
    </div>
  )
}

export default CreateAdmissionPage