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
        teacher_id: data.teacher ?? data.teacher_id,
        started_at: formatDate(data.period_start),
        ended_at: formatDate(data.period_end),
      }
    : undefined

  const handleSubmit = async (values: any) => {
    return handleMutationWithToast({
      action: () =>
        update.mutateAsync({
          id: Number(id),
          data: {
            ...values,
            total_classes:    Number(values.total_classes    || 0),
            gross_amount:     Number(values.gross_amount     || 0),
            deduction_amount: Number(values.deduction_amount || 0),
            amount:           Number(values.amount           || 0),
          },
        }),

      loadingMessage: 'Saving payment...',
      successMessage: 'Teacher payment updated successfully',

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