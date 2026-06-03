import type { PaymentPayload } from '../paymentService'

interface PaymentFormProps {
  defaultValues: PaymentPayload
  loading: boolean
  error?: string
  onChange: (field: keyof PaymentPayload, value: string | number) => void
  onSubmit: () => void
}

const PaymentForm = ({ defaultValues, loading, error, onChange, onSubmit }: PaymentFormProps) => {
  return (
    <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40">
      <h2 className="mb-4 text-xl font-semibold text-slate-900">Payment details</h2>
      {error ? <div className="mb-4 rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{error}</div> : null}
      <div className="space-y-4">
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Student ID</span>
          <input
            value={defaultValues.student_id}
            onChange={(event) => onChange('student_id', Number(event.target.value))}
            type="number"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="123"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Course ID</span>
          <input
            value={defaultValues.course_id}
            onChange={(event) => onChange('course_id', Number(event.target.value))}
            type="number"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="456"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Amount</span>
          <input
            value={defaultValues.amount}
            onChange={(event) => onChange('amount', Number(event.target.value))}
            type="number"
            step="0.01"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="199.99"
          />
        </label>
        <label className="block">
          <span className="mb-2 block text-sm font-medium text-slate-600">Method</span>
          <input
            value={defaultValues.method || ''}
            onChange={(event) => onChange('method', event.target.value)}
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 focus:bg-white"
            placeholder="Card, bank transfer"
          />
        </label>
      </div>
      <button
        type="button"
        onClick={onSubmit}
        disabled={loading}
        className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? 'Processing...' : 'Submit payment'}
      </button>
    </div>
  )
}

export default PaymentForm
