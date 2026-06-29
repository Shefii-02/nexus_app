import { useNavigate } from 'react-router-dom'

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'

import { teacherPaymentFormConfig } from '../teacherPaymentFormConfig'
import { useCreateTeacherPayment } from '../teacherPaymentHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'

const CreateTeacherPaymentPage = () => {
  const navigate = useNavigate()
  const createPayment = useCreateTeacherPayment()

  const handleSubmit = async (data: any) => {
    return handleMutationWithToast({
      action: () =>
        createPayment.mutateAsync({
          ...data,
          total_classes:    Number(data.total_classes    || 0),
          gross_amount:     Number(data.gross_amount     || 0),
          deduction_amount: Number(data.deduction_amount || 0),
          amount:           Number(data.amount           || 0),
        }),

      loadingMessage: 'Creating payment...',
      successMessage: 'Teacher payment created successfully',

      navigate,
      redirect: '/teacher-payments',
    })
  }

  return (
    <div>
      <PageHeader
        title="Create Teacher Payment"
        subtitle="Record a new salary or payment entry for a teacher"
      />

      <div className="pt-4">
        <DynamicForm
          config={teacherPaymentFormConfig}
          onSubmit={handleSubmit}
        />
      </div>
    </div>
  )
}

export default CreateTeacherPaymentPage