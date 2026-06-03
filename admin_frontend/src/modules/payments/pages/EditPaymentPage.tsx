import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { usePayment, useVerifyPayment } from '../paymentHooks'

const EditPaymentPage = () => {
  const { id } = useParams<{ id: string }>()
  const paymentId = Number(id)
  const navigate = useNavigate()
  const { data, isLoading } = usePayment(paymentId)
  const verifyPayment = useVerifyPayment()
  const [message, setMessage] = useState<string | undefined>()

  useEffect(() => {
    if (verifyPayment.isSuccess) {
      setMessage('Payment verified successfully.')
      navigate('/payments')
    }
  }, [verifyPayment.isSuccess, navigate])

  if (isLoading) {
    return <div className="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-sm shadow-slate-200/40">Loading payment details...</div>
  }

  if (!data) {
    return (
      <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
        <h2 className="text-xl font-semibold text-slate-900">Payment not found</h2>
        <p className="mt-2 text-sm text-slate-500">Return to payments list to select a valid payment.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
        <h2 className="text-xl font-semibold text-slate-900">Verify payment</h2>
        <p className="mt-2 text-sm text-slate-500">Review payment details before verifying.</p>
      </div>
      <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
        <div className="grid gap-3 text-sm text-slate-700">
          <div>
            <span className="font-semibold">Student:</span> {data.student_name}
          </div>
          <div>
            <span className="font-semibold">Course:</span> {data.course_title}
          </div>
          <div>
            <span className="font-semibold">Amount:</span> ${data.amount.toFixed(2)}
          </div>
          <div>
            <span className="font-semibold">Status:</span> {data.status}
          </div>
          <div>
            <span className="font-semibold">Paid at:</span> {new Date(data.paid_at).toLocaleDateString()}
          </div>
        </div>
        {message ? <div className="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{message}</div> : null}
        <button
          type="button"
          onClick={() => verifyPayment.mutate(paymentId)}
          disabled={verifyPayment.status === 'pending'}
          className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {verifyPayment.status === 'pending' ? 'Verifying...' : 'Verify payment'}
        </button>
      </div>
    </div>
  )
}

export default EditPaymentPage
