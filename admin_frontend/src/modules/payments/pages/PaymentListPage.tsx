import { useMemo, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PaymentTable from '../components/PaymentTable'
import { usePayments, useVerifyPayment } from '../paymentHooks'
import { useAppSelector } from '../../../store/hooks'

const PaymentListPage = () => {
  const navigate = useNavigate()
  const user = useAppSelector((state) => state.auth.user)
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const { data, isLoading, error } = usePayments({ page, search })
  const verifyPayment = useVerifyPayment()

  const payments = useMemo(
    () =>
      data?.data.filter(
        (payment) =>
          payment.student_name.toLowerCase().includes(search.toLowerCase()) ||
          payment.course_title.toLowerCase().includes(search.toLowerCase()),
      ) || [],
    [data, search],
  )

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/40 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 className="text-xl font-semibold text-slate-900">Payments</h2>
          <p className="mt-1 text-sm text-slate-500">Review transaction history and verify receipts.</p>
        </div>
        <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
          <input
            value={search}
            onChange={(event) => setSearch(event.target.value)}
            placeholder="Search payments"
            className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400 sm:w-80"
          />
          {user?.role === 'admin' ? (
            <button
              type="button"
              onClick={() => navigate('/payments/create')}
              className="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700"
            >
              New payment
            </button>
          ) : null}
        </div>
      </div>

      {error ? (
        <div className="rounded-3xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">Unable to load payments.</div>
      ) : null}

      <PaymentTable
        payments={payments}
        loading={isLoading}
        onVerify={(id) => verifyPayment.mutate(id)}
        userRole={user?.role}
      />

      <div className="flex items-center justify-between rounded-3xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/40">
        <span className="text-sm text-slate-600">Page {page}</span>
        <div className="flex gap-2">
          <button
            type="button"
            onClick={() => setPage((prev) => Math.max(prev - 1, 1))}
            className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100"
          >
            Previous
          </button>
          <button
            type="button"
            onClick={() => setPage((prev) => prev + 1)}
            className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  )
}

export default PaymentListPage
