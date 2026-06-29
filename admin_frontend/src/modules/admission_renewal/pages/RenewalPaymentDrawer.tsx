import { useState } from 'react'
import { useRenewalPayment } from '../RenewalHooks'
import { handleMutationWithToast } from '../../../utils/handleMutationWithToast'


interface Props {
  open: boolean
  renewal: any
  onClose: () => void
}

const RenewalPaymentDrawer = ({ open, renewal, onClose }: Props) => {
  const paymentMutation = useRenewalPayment()

  const [months, setMonths] = useState(1)
  const [discount, setDiscount] = useState(0)
  const [method, setMethod] = useState('cash')
  const [transactionNo, setTransactionNo] = useState('')
  const [remarks, setRemarks] = useState('')

  const courseFee = Number(renewal.course_fee || 0)
  const total = courseFee * months
  const finalAmount = total - discount

  const handleSubmit = () => {
    if (finalAmount < 0) return

    handleMutationWithToast({
      action: () =>
        paymentMutation.mutateAsync({
          admission_id:    renewal.admission_id,
          months,
          discount_amount: discount,
          payment_method:  method,
          transaction_no:  transactionNo,
          remarks,
        }),
      loadingMessage: 'Processing payment...',
      successMessage: 'Renewal completed',
      onSuccess: onClose,
    })
  }

  return (
    <></>
    // <SideDrawer
    //   open={open}
    //   title="Renew Course"
    //   width="w-[480px]"
    //   onClose={onClose}
    // >
    //   <div className="space-y-5 pb-6">

    //     {/* Student Info */}
    //     <div className="bg-gray-50 rounded-xl p-4 space-y-1">
    //       <p className="font-semibold text-gray-900">
    //         {renewal.student_name}
    //       </p>
    //       <p className="text-sm text-gray-500">
    //         {renewal.course_name}
    //       </p>
    //       <p className="text-xs text-gray-400">
    //         Current expiry:{' '}
    //         {renewal.expiry_date
    //           ? format(new Date(renewal.expiry_date), 'dd MMM yyyy')
    //           : '—'}
    //       </p>
    //       {renewal.is_expired && (
    //         <span className="inline-block text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600 font-medium">
    //           Expired — will renew from today
    //         </span>
    //       )}
    //     </div>

    //     {/* Months */}
    //     <div className="space-y-1">
    //       <label className="text-sm font-medium text-gray-700">
    //         Renewal Months
    //       </label>
    //       <input
    //         type="number"
    //         min={1}
    //         value={months}
    //         onChange={(e) => setMonths(Math.max(1, Number(e.target.value)))}
    //         className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black"
    //       />
    //     </div>

    //     {/* Discount */}
    //     <div className="space-y-1">
    //       <label className="text-sm font-medium text-gray-700">
    //         Discount (₹)
    //       </label>
    //       <input
    //         type="number"
    //         min={0}
    //         value={discount}
    //         onChange={(e) => setDiscount(Math.max(0, Number(e.target.value)))}
    //         className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black"
    //       />
    //     </div>

    //     {/* Payment Method */}
    //     <div className="space-y-1">
    //       <label className="text-sm font-medium text-gray-700">
    //         Payment Method
    //       </label>
    //       <select
    //         value={method}
    //         onChange={(e) => setMethod(e.target.value)}
    //         className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black"
    //       >
    //         <option value="cash">Cash</option>
    //         <option value="upi">UPI</option>
    //         <option value="bank">Bank</option>
    //       </select>
    //     </div>

    //     {/* Transaction No */}
    //     {method !== 'cash' && (
    //       <div className="space-y-1">
    //         <label className="text-sm font-medium text-gray-700">
    //           Transaction No
    //         </label>
    //         <input
    //           value={transactionNo}
    //           onChange={(e) => setTransactionNo(e.target.value)}
    //           placeholder="Enter transaction reference"
    //           className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black"
    //         />
    //       </div>
    //     )}

    //     {/* Remarks */}
    //     <div className="space-y-1">
    //       <label className="text-sm font-medium text-gray-700">
    //         Remarks
    //       </label>
    //       <textarea
    //         value={remarks}
    //         onChange={(e) => setRemarks(e.target.value)}
    //         rows={2}
    //         placeholder="Optional note"
    //         className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black resize-none"
    //       />
    //     </div>

    //     {/* Summary */}
    //     <div className="bg-green-50 rounded-xl p-4 space-y-2 text-sm">
    //       <div className="flex justify-between text-gray-600">
    //         <span>Fee / Month</span>
    //         <span>₹{courseFee.toLocaleString()}</span>
    //       </div>
    //       <div className="flex justify-between text-gray-600">
    //         <span>Months</span>
    //         <span>{months}</span>
    //       </div>
    //       <div className="flex justify-between text-gray-600">
    //         <span>Total</span>
    //         <span>₹{total.toLocaleString()}</span>
    //       </div>
    //       <div className="flex justify-between text-gray-600">
    //         <span>Discount</span>
    //         <span>- ₹{discount.toLocaleString()}</span>
    //       </div>
    //       <div className="flex justify-between font-bold text-gray-900 border-t border-green-200 pt-2">
    //         <span>Final Amount</span>
    //         <span>₹{finalAmount.toLocaleString()}</span>
    //       </div>
    //     </div>

    //     {/* Submit */}
    //     <button
    //       disabled={paymentMutation.isPending || finalAmount < 0}
    //       onClick={handleSubmit}
    //       className="w-full bg-black text-white py-3 rounded-xl text-sm font-medium hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    //     >
    //       {paymentMutation.isPending ? 'Processing...' : 'Receive Payment'}
    //     </button>

    //   </div>
    // </SideDrawer>
  )
}

export default RenewalPaymentDrawer