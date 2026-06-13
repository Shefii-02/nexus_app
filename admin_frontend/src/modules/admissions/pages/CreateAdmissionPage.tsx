import { useNavigate } from 'react-router-dom'

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'

import { admissionFormConfig } from '../admissionFormConfig'

import { useCreateAdmission } from '../admissionHooks'

import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CreateAdmissionPage = () => {

  const navigate =
    useNavigate()

  const createAdmission =
    useCreateAdmission()

  const handleSubmit =
    async (data: any) => {

      return handleMutationWithToast({

        action: () =>
          createAdmission.mutateAsync(
            data
          ),

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