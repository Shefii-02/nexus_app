import Dropdown from '../../../components/Dropdown'
import { MoreVertical } from 'lucide-react'
import type { TeacherPayment } from '../teacherPaymentService'

interface Props {
  data: TeacherPayment[]
  loading: boolean
  onView: (id: number) => void
  onEdit: (id: number) => void
  onDelete: (id: number) => void
}

const PaymentTable = ({ data, loading, onView, onEdit, onDelete }: Props) => {
  return (
    <div className="overflow-visible border rounded-xl bg-white">
      <table className="w-full text-sm">
        <thead className="bg-gray-50">
          <tr>
            <th className="p-3 text-left">Teacher</th>
            <th className="p-3 text-left">Period</th>
            <th className="p-3 text-left">Gross Amount</th>
            <th className="p-3 text-left">Deduction</th>
            <th className="p-3 text-left">Transfer Amount</th>
            <th className="p-3 text-left">Payment Date</th>
            <th className="p-3 text-left">Method</th>
            <th className="p-3 text-left">Status</th>
            <th className="p-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody>
          {loading ? (
            <tr>
              <td colSpan={9} className="p-4 text-center text-gray-400">
                Loading...
              </td>
            </tr>
          ) : data.length === 0 ? (
            <tr>
              <td colSpan={9} className="p-4 text-center text-gray-400">
                No payment records found
              </td>
            </tr>
          ) : (
            data.map((p) => (
              <tr key={p.id} className="border-t hover:bg-gray-50 transition-colors">
                <td className="p-3">
                  <div className="font-medium">{p.teacher?.name || '-'}</div>
                  <div className="text-gray-400 text-xs">{p.teacher?.email || '-'}</div>
                </td>
                <td className="p-3">
                  <div>{p.period_start}</div>
                  <div className="text-gray-400 text-xs">to {p.period_end}</div>
                </td>
                <td className="p-3">₹{p.gross_amount.toLocaleString()}</td>
                <td className="p-3 text-red-500">-₹{p.deduction_amount.toLocaleString()}</td>
                <td className="p-3 font-medium text-green-600">₹{p.transfer_amount.toLocaleString()}</td>
                <td className="p-3">{p.payment_date || '-'}</td>
                <td className="p-3 capitalize">{p.payment_method || '-'}</td>
                <td className="p-3">
                  <span
                    className={`px-2 py-1 rounded-full text-xs font-medium ${
                      p.status === 'released'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-yellow-100 text-yellow-700'
                    }`}
                  >
                    {p.status}
                  </span>
                </td>
                <td className="p-3 text-right">
                  <Dropdown
                    trigger={
                      <button className="p-2 hover:bg-gray-100 rounded">
                        <MoreVertical size={18} />
                      </button>
                    }
                    items={[
                      { label: 'View',   onClick: () => onView(p.id) },
                      { label: 'Edit',   onClick: () => onEdit(p.id) },
                      { label: 'Delete', danger: true, onClick: () => onDelete(p.id) },
                    ]}
                  />
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