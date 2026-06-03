import type { Payment } from '../paymentService'

interface PaymentTableProps {
  payments: Payment[]
  loading: boolean
  onVerify: (id: number) => void
  userRole?: string
}

const PaymentTable = ({ payments, loading, onVerify, userRole }: PaymentTableProps) => {
  return (
    <div className="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/40">
      <table className="min-w-full divide-y divide-slate-200 text-sm">
        <thead className="bg-slate-50 text-slate-700">
          <tr>
            <th className="px-6 py-4 text-left font-semibold">Student</th>
            <th className="px-6 py-4 text-left font-semibold">Course</th>
            <th className="px-6 py-4 text-left font-semibold">Amount</th>
            <th className="px-6 py-4 text-left font-semibold">Status</th>
            <th className="px-6 py-4 text-left font-semibold">Paid</th>
            <th className="px-6 py-4 text-right font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-200">
          {loading ? (
            <tr>
              <td colSpan={6} className="p-6 text-center text-slate-500">Loading payments...</td>
            </tr>
          ) : payments.length === 0 ? (
            <tr>
              <td colSpan={6} className="p-6 text-center text-slate-500">No payments found.</td>
            </tr>
          ) : (
            payments.map((payment) => (
              <tr key={payment.id}>
                <td className="px-6 py-4 text-slate-900">{payment.student_name}</td>
                <td className="px-6 py-4 text-slate-600">{payment.course_title}</td>
                <td className="px-6 py-4 text-slate-600">${payment.amount.toFixed(2)}</td>
                <td className="px-6 py-4 text-slate-600">{payment.status}</td>
                <td className="px-6 py-4 text-slate-600">{new Date(payment.paid_at).toLocaleDateString()}</td>
                <td className="px-6 py-4 text-right text-slate-700">
                 
                    <button
                      type="button"
                      onClick={() => onVerify(payment.id)}
                      className="inline-flex rounded-2xl border border-slate-200 bg-slate-100 px-3 py-1 text-sm text-slate-700 transition hover:bg-slate-200"
                    >
                      Verify
                    </button>
                
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

export default PaymentTable
