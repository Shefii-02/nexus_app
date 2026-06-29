import { useNavigate } from 'react-router-dom'

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'

import { staffPaymentFormConfig } from '../staffPaymentFormConfig'
import { useCreateStaffPayment } from '../staffPaymentHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import Button from '../../../components/Button'

const CreateStaffPaymentPage = () => {
  const navigate = useNavigate()
  const createPayment = useCreateStaffPayment()

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
      successMessage: 'Staff payment created successfully',

      navigate,
      redirect: '/staff-payments',
    })
  }

  return (
    <div>
      <PageHeader
        title="Create Staff Payment"
        subtitle="Record a new salary or payment entry for a staff"
        actions={
          <Button onClick={() => navigate('/staff-payments')}>
             Back Payments
          </Button>
        }
      />

      <div className="pt-4">
        <DynamicForm
          config={staffPaymentFormConfig}
          onSubmit={handleSubmit}
        />
      </div>
    </div>
  )
}

export default CreateStaffPaymentPage