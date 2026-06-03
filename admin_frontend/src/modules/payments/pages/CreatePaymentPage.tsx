import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PaymentForm from '../components/PaymentForm'
import { useCreatePayment } from '../paymentHooks'

const CreatePaymentPage = () => {
  const navigate = useNavigate()
  const [values, setValues] = useState({ student_id: 0, course_id: 0, amount: 0, method: '' })
  const [error, setError] = useState<string | undefined>()
  const createPayment = useCreatePayment()

  const handleChange = (field: keyof typeof values, value: string | number) => {
    setValues((prev) => ({ ...prev, [field]: value }))
  }

  const handleSubmit = () => {
    if (!values.student_id || !values.course_id || !values.amount) {
      setError('Student, course, and amount are required.')
      return
    }
    setError(undefined)
    createPayment.mutate(values, {
      onSuccess: () => navigate('/payments'),
      onError: () => setError('Unable to create payment.'),
    })
  }

  return (
    <div className="space-y-6">
      <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
        <h2 className="text-xl font-semibold text-slate-900">Create Payment</h2>
        <p className="mt-2 text-sm text-slate-500">Record a payment for a student and course.</p>
      </div>
      <PaymentForm defaultValues={values} loading={createPayment.status === 'pending'} error={error} onChange={handleChange} onSubmit={handleSubmit} />
    </div>
  )
}

export default CreatePaymentPage
