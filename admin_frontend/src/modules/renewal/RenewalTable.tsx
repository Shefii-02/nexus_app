import { AlertCircle, Clock } from 'lucide-react'

interface RenewalRow {
  admission_id: number
  student_name: string
  course_name: string
  course_fee: number
  expiry_date: string
  is_expired: boolean
  // history-only fields
  months?: number
  final_amount?: number
  payment_method?: string
  renewed_from?: string
  renewed_until?: string
}

interface Props {
  data: RenewalRow[]
  loading: boolean
  hidePayButton?: boolean
  onPay?: (row: RenewalRow) => void
}

const RenewalTable = ({
  data,
  loading,
  hidePayButton = false,
  onPay,
}: Props) => {
  if (loading) {
    return (
      <div className="space-y-3">
        {[...Array(5)].map((_, i) => (
          <div
            key={i}
            className="h-16 bg-gray-100 rounded-xl animate-pulse"
          />
        ))}
      </div>
    )
  }

  if (!data.length) {
    return (
      <div className="text-center py-16 text-gray-400">
        <Clock className="mx-auto mb-2 w-8 h-8" />
        <p>No records found</p>
      </div>
    )
  }

  return (
    <div className="overflow-x-auto rounded-xl border border-gray-200">
      <table className="w-full text-sm">
        <thead className="bg-gray-50 text-gray-500 text-xs uppercase">
          <tr>
            <th className="px-4 py-3 text-left">Student</th>
            <th className="px-4 py-3 text-left">Course</th>
            {hidePayButton ? (
              <>
                <th className="px-4 py-3 text-left">Months</th>
                <th className="px-4 py-3 text-left">Renewed From</th>
                <th className="px-4 py-3 text-left">Renewed Until</th>
                <th className="px-4 py-3 text-left">Method</th>
                <th className="px-4 py-3 text-right">Amount</th>
              </>
            ) : (
              <>
                <th className="px-4 py-3 text-left">Fee / Month</th>
                <th className="px-4 py-3 text-left">Expiry</th>
                <th className="px-4 py-3 text-left">Status</th>
                <th className="px-4 py-3 text-right">Action</th>
              </>
            )}
          </tr>
        </thead>
        <tbody className="divide-y divide-gray-100">
          {data.map((row, i) => (
            <tr key={i} className="hover:bg-gray-50 transition-colors">
              <td className="px-4 py-3 font-medium text-gray-900">
                {row.student_name}
              </td>
              <td className="px-4 py-3 text-gray-600">
                {row.course_name}
              </td>

              {hidePayButton ? (
                <>
                  <td className="px-4 py-3 text-gray-600">
                    {row.months} month(s)
                  </td>
                  <td className="px-4 py-3 text-gray-600">
                    {/* {row.renewed_from
                      ? format(new Date(row.renewed_from), 'dd MMM yyyy')
                      : '—'} */}
                  </td>
                  <td className="px-4 py-3 text-gray-600">
                    {/* {row.renewed_until
                      ? format(new Date(row.renewed_until), 'dd MMM yyyy')
                      : '—'} */}
                  </td>
                  <td className="px-4 py-3 capitalize text-gray-600">
                    {row.payment_method || '—'}
                  </td>
                  <td className="px-4 py-3 text-right font-semibold text-gray-900">
                    ₹{row.final_amount?.toLocaleString()}
                  </td>
                </>
              ) : (
                <>
                  <td className="px-4 py-3 text-gray-600">
                    ₹{Number(row.course_fee).toLocaleString()}
                  </td>
                  <td className="px-4 py-3 text-gray-600">
                    {/* {row.expiry_date
                      ? format(new Date(row.expiry_date), 'dd MMM yyyy')
                      : '—'} */}
                  </td>
                  <td className="px-4 py-3">
                    {row.is_expired ? (
                      <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                        <AlertCircle className="w-3 h-3" />
                        Expired
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-600">
                        <Clock className="w-3 h-3" />
                        Due Soon
                      </span>
                    )}
                  </td>
                  <td className="px-4 py-3 text-right">
                    <button
                      onClick={() => onPay?.(row)}
                      className="px-3 py-1.5 bg-black text-white text-xs rounded-lg hover:bg-gray-800 transition-colors"
                    >
                      Renew
                    </button>
                  </td>
                </>
              )}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default RenewalTable