import { useParams } from 'react-router-dom'

import PageHeader from '../../../components/PageHeader'
import { useTeacherPayment } from '../teacherPaymentHooks'

const ViewTeacherPaymentPage = () => {
  const { id } = useParams()
  const { data } = useTeacherPayment(Number(id))

  if (!data) return null

  return (
    <div className="space-y-6">
      <PageHeader title="Teacher Payment Details" />

      <div className="bg-white rounded-xl p-5 shadow space-y-3">
        <div>
          <span className="text-gray-500 text-sm">Teacher:</span>{' '}
          <span className="font-medium">{data.teacher?.name ?? '-'}</span>
        </div>
        <div>
          <span className="text-gray-500 text-sm">Period:</span>{' '}
          {data.period_start} — {data.period_end}
        </div>
        <div>
          <span className="text-gray-500 text-sm">Total Classes:</span>{' '}
          {data.total_classes}
        </div>
        <div>
          <span className="text-gray-500 text-sm">Gross Amount:</span>{' '}
          ₹{data.gross_amount?.toLocaleString()}
        </div>
        <div>
          <span className="text-gray-500 text-sm">Deduction:</span>{' '}
          <span className="text-red-500">
            -₹{data.deduction_amount?.toLocaleString()}
          </span>
          {data.deduction_reason && (
            <span className="text-gray-400 text-xs ml-2">
              ({data.deduction_reason})
            </span>
          )}
        </div>
        <div>
          <span className="text-gray-500 text-sm">Transfer Amount:</span>{' '}
          <span className="text-green-600 font-medium">
            ₹{data.amount?.toLocaleString()}
          </span>
        </div>
        <div>
          <span className="text-gray-500 text-sm">Payment Method:</span>{' '}
          {data.payment_method ?? '-'}
        </div>
        <div>
          <span className="text-gray-500 text-sm">Transaction No:</span>{' '}
          {data.transaction_no ?? '-'}
        </div>
        {data.payment_reference && (
          <div>
            <span className="text-gray-500 text-sm">Payment Reference:</span>{' '}
            {data.payment_reference}
          </div>
        )}
        <div>
          <span className="text-gray-500 text-sm">Payment Date:</span>{' '}
          {data.payment_date ?? '-'}
        </div>
        {data.remarks && (
          <div>
            <span className="text-gray-500 text-sm">Remarks:</span>{' '}
            {data.remarks}
          </div>
        )}
        <div>
          <span className="text-gray-500 text-sm">Status:</span>{' '}
          <span
            className={`px-2 py-0.5 rounded-full text-xs font-medium ${
              data.status === 'released'
                ? 'bg-green-100 text-green-700'
                : 'bg-yellow-100 text-yellow-700'
            }`}
          >
            {data.status}
          </span>
        </div>
      </div>
    </div>
  )
}

export default ViewTeacherPaymentPage