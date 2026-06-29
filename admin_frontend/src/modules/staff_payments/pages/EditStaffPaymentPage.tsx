import { useNavigate, useParams } from 'react-router-dom'

import DynamicForm from '../../../components/DynamicForm'
import PageHeader from '../../../components/PageHeader'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'
import Button from '../../../components/Button'

import {
  useStaffPayment,
  useUpdateStaffPayment,
} from '../staffPaymentHooks'

import { staffPaymentFormConfig } from '../staffPaymentFormConfig'

const EditStaffPaymentPage = () => {
  const { id } = useParams()
  const navigate = useNavigate()

  const { data } = useStaffPayment(Number(id))
  const update = useUpdateStaffPayment()

    /** =========================
   * FORMAT DATE
   ========================= */
  const formatDate = (date?: string) => {
    if (!date) return ''
    return date.split('T')[0] // handles both date & datetime
  }


  // Build defaultValues: replace teacher_id (number) with the full teacher
  // object so AsyncSelectField can display the selected teacher's name.
  const defaultValues = data
    ? {
        ...data,
        // Pass the nested teacher object; AsyncSelectField will extract .id
        // for the form value and show .name as the label.
        staff_id: data.staff ?? data.staff_id,
        salary_month: data.salary_month,
      }
    : undefined

  const handleSubmit = async (values: any) => {
    return handleMutationWithToast({
      action: () =>
        update.mutateAsync({
          id: Number(id),
          data: {
            ...values,
            salary_amount:     Number(values.salary_amount     || 0),
            deduction_amount: Number(values.deduction_amount || 0),
            final_amount:           Number(values.final_amount           || 0),
          },
        }),

      loadingMessage: 'Saving payment...',
      successMessage: 'Staff payment updated successfully',

      navigate,
      redirect: '/staff-payments',
    })
  }

  return (
    <div>
      <PageHeader title="Edit Teacher Payment" 
       actions={
          <Button onClick={() => navigate('/teacher-payments')}>
             Back Payments
          </Button>
        }
      />

      <DynamicForm
        config={staffPaymentFormConfig}
        defaultValues={defaultValues}
        onSubmit={handleSubmit}
      />
    </div>
  )
}

export default EditStaffPaymentPage